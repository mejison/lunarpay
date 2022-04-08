<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;

function inStatusAsHtmlString($invoiceStatus) {
    return Invoice_model::INVOICE_STATUS_STRING_HTML[$invoiceStatus];
}

function inAllowEditInvoice($invoiceStatus) {
    if($invoiceStatus == Invoice_model::INVOICE_DRAFT_STATUS) {
        return 1;
    }    
    return 0;
}
   
function inAllowRemoveInvoice($invoiceStatus) {
    if($invoiceStatus == Invoice_model::INVOICE_DRAFT_STATUS) {
        return 1;
    }
    return 0;
}

function inAllowSendEmail($invoiceStatus) {
    if($invoiceStatus == Invoice_model::INVOICE_UNPAID_STATUS) {
        return 1;
    }    
    return 0;
}

function inAllowClone($invoiceStatus) {
    if($invoiceStatus == Invoice_model::INVOICE_PAID_STATUS || $invoiceStatus == Invoice_model::INVOICE_CANCELED_STATUS) {
        return 1;
    }    
    return 0;
}

function inCoverFee($coverFee, $fee) {
    if($coverFee) {
        return '$' . $fee;
    }    
    return 'No';
}

class Invoice_model extends CI_Model {

    const INVOICE_DRAFT_STATUS    = 'D';
    const INVOICE_UNPAID_STATUS   = 'U';
    const INVOICE_PAID_STATUS     = 'P';
    const INVOICE_DUE_STATUS      = 'E';
    const INVOICE_CANCELED_STATUS = 'C';
    
    const INVOICE_STATUS_STRING = [//For presenting to the final user*
        Invoice_model::INVOICE_DRAFT_STATUS    => 'Draft',
        Invoice_model::INVOICE_UNPAID_STATUS   => 'Open',
        Invoice_model::INVOICE_PAID_STATUS     => 'Paid',
        Invoice_model::INVOICE_DUE_STATUS      => 'Past Due',
        Invoice_model::INVOICE_CANCELED_STATUS => 'Canceled'
    ];
    
    const INVOICE_STATUS_STRING_HTML = [//For presenting to the final user as html badge
        Invoice_model::INVOICE_DRAFT_STATUS    => '<span class="badge badge-secondary" style="width: 60px">Draft</span>',
        Invoice_model::INVOICE_UNPAID_STATUS   => '<span class="badge badge-primary" style="width: 60px">Open</span>',
        Invoice_model::INVOICE_PAID_STATUS     => '<span class="badge badge-success" style="width: 60px">Paid</span>',
        Invoice_model::INVOICE_DUE_STATUS      => '<span class="badge badge-warning" style="width: 60px">Past Due</span>',
        Invoice_model::INVOICE_CANCELED_STATUS => '<span class="badge" style="width: 60px">Canceled</span>'
    ];

    const METAMASK_ADDRESS = '0x9F7846769e634f4A408dd8fbDaaeb49728A548Be';

    private $table       = 'invoices';
    public $valAsArray   = false; //for getting validation errors as array or a string, false = string
    
    //invoice hash =>>> hash = invoice_id + hash, so the total hash size will be the length of invoice_id + length of HASH_SIZE
    const HASH_SIZE = 128; 

    public function __construct() {
        parent::__construct();
    }

    private function beforeSave($data){
        if(isset($data['memo']) && $data['memo']){
            $data['memo'] = ucfirst(trimLR_Duplicates($data['memo']));
        }

        if(isset($data['footer']) && $data['footer']){
            $data['footer'] = ucfirst(trimLR_Duplicates($data['footer']));
        }
        return $data;
    }
    
    private function addStatusAsString($invoice) {

        $invoice->_status = Invoice_model::INVOICE_STATUS_STRING[$invoice->status];
        return $invoice;
    }
    private function addStatusAsHtml($invoice) {
        $invoice->_statusHtml = Invoice_model::INVOICE_STATUS_STRING_HTML[$invoice->status];
        return $invoice;
    }

    public function getDt() {
        $user_id  = $this->session->userdata('user_id');
        $orgnx_id = $this->input->post('organization_id');
        $suborgnx_id = $this->input->post('sub_organization_id');
        $this->load->library("Datatables");
        if ($suborgnx_id) {
            $this->datatables->where('i.campus_id', $suborgnx_id);
        }
        if ($orgnx_id) {
            $this->datatables->where('i.church_id', $orgnx_id ); //church = organization, campus= suborganization
        }
        $this->datatables->select("i.id, i.reference, i.total_amount, i.status, i.hash, i.pdf_url, i.cover_fee, i.fee, (i.total_amount + i.fee) bigTotal,
                     CONCAT_WS(' - ', CONCAT_WS(' ',d.first_name, d.last_name), d.email) as customer , 
                     DATE_FORMAT(i.created_at, '%m/%d/%Y') as created_at_formatted, i.created_at,
                     DATE_FORMAT(i.due_date, '%m/%d/%Y') as due_date_formatted, i.due_date,
                     c.ch_id as org_id, c.church_name as org_name, cm.id as suborg_id, cm.name as suborg_name")
            ->join('church_detail c', 'c.ch_id = i.church_id', 'INNER')
            ->join('account_donor d', 'd.id = i.donor_id', 'INNER') // donor = customer
            ->join('campuses cm','cm.id = i.campus_id','left')
            ->where('c.client_id', $user_id)
            ->from($this->table . ' i');
        
        $this->datatables->add_column('allowClone', '$1', 'inAllowClone(status)');
        $this->datatables->add_column('allowEdit', '$1', 'inAllowEditInvoice(status)');
        $this->datatables->add_column('allowRemove', '$1', 'inAllowRemoveInvoice(status)');
        $this->datatables->add_column('allowSendEmail', '$1', 'inAllowSendEmail(status)');
        
        $this->datatables->edit_column('status', '$1', 'inStatusAsHtmlString(status)');
        $this->datatables->add_column('coverFee', '$1', 'inCoverFee(cover_fee, fee)');
        $data = $this->datatables->generate();
        return $data;
    }

    public function getByHash($hash, $client_id = false) {
        if($client_id){
            $orgnx_ids     = getOrganizationsIds($client_id);
            $this->db->where_in('church_id', explode(',', $orgnx_ids));
        }

        $this->db->where('hash', $hash);
        $invoice = $this->db->where('hash', $hash)->get($this->table)->row();

        if ($invoice) {
            
            $this->load->model('donation_model');
            $this->load->model('invoice_products_model');
            
            $invoice = $this->addStatusAsString($invoice);
           
            $products = $this->invoice_products_model->getList($invoice->id);
            $payments = $this->donation_model->getByInvoice($invoice);

            $invoice->products = $products ? $products : [];
            $invoice->payments = $payments ? $payments : [];
            
            // ---- get organization, suborganization, an customer data all from an invoice. All data in one package
            $this->load->model('organization_model');
            $invoice->organization = $this->organization_model->get($invoice->church_id, 
                    'ch_id, client_id, church_name as name, phone_no, website, street_address, street_address_suite, city, state, postal, paysafe_template');
            
            $this->load->helper('paysafe');
            $invoice->organization->fees_template = getPaySafeTplParams($invoice->organization->paysafe_template);
            
            $this->load->model('orgnx_onboard_psf_model');
            $onboard = $this->orgnx_onboard_psf_model->getByOrg($invoice->organization->ch_id, $invoice->organization->client_id, 'region, currency');
            
            $invoice->organization->region = $onboard->region;
            $invoice->organization->currency = $onboard->currency;
            
            $this->load->model('suborganization_model');
            $invoice->suborganization = $this->suborganization_model->get($invoice->campus_id, false, 'name, phone as phone_no');
            
            $this->load->model('donor_model');
            $invoice->customer = $this->donor_model->get($invoice->donor_id, 'first_name, last_name, email,', true);
        }

        return $invoice;
    }

    public function getById($id, $client_id = false) {
        
        //check if to add security is needed for this method
        $client_id = $client_id ? $client_id : $this->session->userdata('user_id');
        $orgnx_ids     = getOrganizationsIds($client_id);
        
        //being sure the invoice belongs to the user, based on his organizations
        $invoice = $this->db->where('id', $id)
                ->where_in('church_id', explode(',', $orgnx_ids))
                ->get($this->table)->row();
        
        if ($invoice) {
            
            $this->load->model('donation_model');
            $this->load->model('invoice_products_model');            
            
            $invoice = $this->addStatusAsString($invoice);
            $invoice = $this->addStatusAsHtml($invoice);
            
            $products = $this->invoice_products_model->getList($invoice->id);

            $payments = $this->donation_model->getByInvoice($invoice);

            $invoice->products = $products ? $products : [];   
            $invoice->payments = $payments ? $payments : [];            
            
            // ---- get organization, suborganization, an customer data all from an invoice. All data in one package
            $this->load->model('organization_model');
            $invoice->organization = $this->organization_model->get($invoice->church_id, 
                    'ch_id, client_id, church_name as name, phone_no, website, street_address, street_address_suite, city, state, postal, paysafe_template');                    
            
            $this->load->helper('paysafe');
            $invoice->organization->fees_template = getPaySafeTplParams($invoice->organization->paysafe_template);
            
            $this->load->model('orgnx_onboard_psf_model');
            $onboard = $this->orgnx_onboard_psf_model->getByOrg($invoice->organization->ch_id, $invoice->organization->client_id, 'region');
            
            $invoice->organization->region = $onboard->region;
            
            $this->load->model('suborganization_model');
            $invoice->suborganization = $this->suborganization_model->get($invoice->campus_id, false, 'name, phone as phone_no');
            
            $this->load->model('donor_model');
            $invoice->customer = $this->donor_model->get($invoice->donor_id, 'first_name, last_name, email,', true);
            
            //$this->load->model('user_model');
            //$client_id = $invoice->organization->client_id;
            //$this->user_model->get($client_id, 'id, email, first_name, last_name'); add this to the object           
            
        }

        return $invoice;
    }

    public function clone_invoice($id){
        $data = $this->invoice_model->getById($id);
        $save_data = [
            'organization_id'          => $data->church_id,
            'suborganization_id'          => $data->campus_id ? $data->campus_id : null,
            'account_donor_id'           => $data->donor_id,
            'due_date'           => date('Y-m-d', strtotime(date('Y-m-d').' + '.$this->_dateDiff($data->due_date,$data->created_at).' days')),
            'memo'               => $data->memo,
            'footer'             => $data->footer,
            'payment_options'    => json_decode($data->payment_options),
            'cover_fee'          => $data->cover_fee,
        ];
        $product_id = [];
        $quantity = [];
       
        foreach ($data->products as $product) {
            array_push($product_id,$product->product_id);
            array_push($quantity,$product->quantity);
        }

        $save_data['product_id'] = $product_id;
        $save_data['quantity'] = $quantity;
        $save_data['command'] = 'save_only';
               
        $result = $this->save($save_data);
        
        if($result['status']) {
            $result['message'] = langx('Invoice cloned');  //override success message, it's being cloned
            $result['message2'] = langx('Reloading ...'); // just adding a second message
        }
        
        return $result;
    }

    private function _dateDiff ($d1, $d2) {  //return difference in days from two dates
        $diff = round(abs(strtotime($d1) - strtotime($d2))/86400,1);
        $days = explode(".",$diff);
        return $days[1] > 0 ? $days[0]+1 : $days[0];
    } 

    public function save($data, $client_id = false) {
       
        $data['hash'] = isset($data['hash']) ? $data['hash'] : null;
        $val_messages    = [];
        if(!$data['hash']) {
            if (!isset($data['organization_id']) || !$data['organization_id'])
                $val_messages [] = langx('The Company field is required');
        }
        if (!isset($data['account_donor_id']) || !$data['account_donor_id'])
            $val_messages [] = langx('The Customer field is required');

        if (!isset($data['due_date']) || !$data['due_date'])
            $val_messages [] = langx('The Due Date field is required');

        if($data['command'] == 'save_and_send'){
            if(!isset($data['product_id'])) {
                $val_messages [] = langx('At least one Product is required to send the invoice');
            }
        }
        
        $cover_fee = null;
        if(isset($data['cover_fee']) && $data['cover_fee']) {
            $this->load->model('organization_model');
            $this->load->helper('paysafe');                        
            $cover_fee = $data['cover_fee'];
        }

        if (empty($val_messages)) {
            $client_id = $client_id ? $client_id : $this->session->userdata('user_id');

            // ---- Validating that the user sends an organization that belongs to him
            $orgnx_ids     = getOrganizationsIds($client_id);
            $orgnx_ids_arr = $orgnx_ids ? explode(',', $orgnx_ids) : [];
            if (!in_array($data['organization_id'], $orgnx_ids_arr)) {
                throw new Exception('Invalid organization');
            }

            //Defining Status
            $status = self::INVOICE_DRAFT_STATUS;
            if($data['command'] == 'save_and_send'){
                $status = self::INVOICE_UNPAID_STATUS;
            }

            $campus_id = isset($data['suborganization_id']) ? (int)$data['suborganization_id'] : null;
            
            if(!$data['hash']) { // Create Invoice
                
                $bytes = openssl_random_pseudo_bytes(self::HASH_SIZE / 2, $cstrong);
                $hash  = bin2hex($bytes);
                
                $save_data = [
                    'church_id'          => $data['organization_id'],
                    'campus_id'          => $campus_id ? $campus_id : null,
                    'donor_id'           => $data['account_donor_id'],
                    'due_date'           => date('Y-m-d', strtotime($data['due_date'])),
                    'memo'               => $data['memo'],
                    'footer'             => $data['footer'],
                    'payment_options'    => isset($data['payment_options'])? json_encode($data['payment_options']) : null,
                    'status'             => $status,
                    'cover_fee'          => $cover_fee,
                    'created_at'         => date('Y-m-d H:i:s')
                ];

                $save_data = $this->beforeSave($save_data);

                $this->db->trans_start();

                $this->db->insert($this->table, $save_data);

                $invoice_id = $this->db->insert_id();                
                
                
                $this->load->model('product_model');
                $this->load->model('invoice_products_model');

                $totalAmount = 0;

                if(isset($data['product_id'])) {
                    foreach ($data['product_id'] as $key => $product_id) {
                        $product = $this->product_model->get($product_id, $client_id);
                       
                        $data_detail = [
                            'product_id' => $product_id,
                            'quantity' => $data['quantity'][$key],
                            'invoice_id' => $invoice_id,
                            'price' => (float)$product->price,
                            'product_name' => $product->name
                        ];

                        $totalAmount += $data_detail['quantity'] * $data_detail['price'];

                        $this->invoice_products_model->save($data_detail);
                    }
                }
                                
                $fee = 0;
                if ($cover_fee) {
                    $orgnx         = $this->organization_model->get($data['organization_id'], 'paysafe_template');
                    $this->load->helper('paysafe');
                    $fees_template = getPaySafeTplParams($orgnx->paysafe_template);
                    $newTotal      = ($totalAmount + $fees_template['kte_cc']) / (1 - $fees_template['var_cc']);
                    $fee = round($newTotal - $totalAmount, 2);
                }

                $hexa = strtoupper(dechex(date('ymdHi'))); //two digits year, month, hour & minute converted to hexa
                
                $hash      = $invoice_id . $hash;
                $reference = 'IN' . $hexa . '-00' .$invoice_id; 
                $this->db->where('id', $invoice_id)->update($this->table, ['reference' => $reference, 'hash' => $hash, 'total_amount' => $totalAmount, 'fee' => $fee]); //granting uniqueness, updating total amount

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Database transaction error');
                }
                
                if($status == self::INVOICE_UNPAID_STATUS) { // Send Email                    
                    $invoiceFullData = $this->invoice_model->getByHash($hash);
                    
                    $invoiceFullData->finalized = date('Y-m-d H:i:s'); //update object with the finalized date and save it on the database
                    $invoiceFullData->pdf_url = $this->createPdf($invoiceFullData); //update object with pdf_url and save it on the database
                    $this->db->where('id', $invoice_id)->update($this->table, ['pdf_url ' => $invoiceFullData->pdf_url, 'finalized' => $invoiceFullData->finalized]);
                    
                    $this->load->helper('emails');
                    if(isset($data['optional_email'])){
                        $invoiceFullData->_cc = $data['optional_email'];
                    }
                    $result = sendInvoiceEmail($invoiceFullData,'pay');
                    return [
                        'status'       => true,
                        'message'      => langx('Invoice Created'),
                        'emailResponse' => $result
                    ];
                } else { // Just Saving
                    return [
                        'status'  => true,
                        'message' => langx('Invoice Created'),
                        'data' => ['id' => $invoice_id]
                    ];
                }
            } else { //Update Invoice
                $save_data = [
                    'donor_id'           => $data['account_donor_id'],
                    'memo'               => $data['memo'],
                    'footer'             => $data['footer'],
                    'due_date'           => date('Y-m-d', strtotime($data['due_date'])),
                    'payment_options'    => isset($data['payment_options'])? json_encode($data['payment_options']) : null,
                    'status'             => $status,
                    'cover_fee'          => $cover_fee,
                    'updated_at'         => date('Y-m-d H:i:s')
                ];

                $save_data = $this->beforeSave($save_data);

                $this->db->trans_start();

                if($data['hash'])
                    $this->db->where('hash', $data['hash']);

                $this->db->update($this->table, $save_data);

                $this->load->model('product_model');
                $this->load->model('invoice_products_model');

                $totalAmount = 0;
                $invoiceFullData = $this->invoice_model->getByHash($data['hash']);

                $this->invoice_products_model->removeAllInvoice($invoiceFullData->id);

                if(isset($data['product_id'])) {
                    foreach ($data['product_id'] as $key => $product_id) {
                        $product = $this->product_model->get($product_id, $client_id);

                        $data_detail = [
                            'product_id' => $product_id,
                            'quantity' => $data['quantity'][$key],
                            'invoice_id' => $invoiceFullData->id,
                            'price' => (float)$product->price,
                            'product_name' => $product->name
                        ];

                        $totalAmount += $data_detail['quantity'] * $data_detail['price'];

                        $this->invoice_products_model->save($data_detail);
                    }
                }

                $fee = 0;
                if ($cover_fee) {
                    $orgnx         = $this->organization_model->get($data['organization_id'], 'paysafe_template');
                    $this->load->helper('paysafe');
                    $fees_template = getPaySafeTplParams($orgnx->paysafe_template);
                    $newTotal      = ($totalAmount + $fees_template['kte_cc']) / (1 - $fees_template['var_cc']);
                    $fee = round($newTotal - $totalAmount, 2);
                }
                
                $this->db->where('id', $invoiceFullData->id)->update($this->table, ['total_amount' => $totalAmount, 'fee' => $fee]); //updating total amount

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Database transaction error');
                }

                if($status == self::INVOICE_UNPAID_STATUS) { // Send Email
                    $invoiceFullData = $this->invoice_model->getByHash($data['hash']); //get updates
                    
                    $invoiceFullData->finalized = date('Y-m-d H:i:s'); //update object with the finalized date and save it on the database
                    $invoiceFullData->pdf_url = $this->createPdf($invoiceFullData); //update object with pdf_url and save it on the database
                    $this->db->where('id', $invoiceFullData->id)->update($this->table, ['pdf_url ' => $invoiceFullData->pdf_url, 'finalized' => $invoiceFullData->finalized]);
                    
                    $this->load->helper('emails');
                    $result = sendInvoiceEmail($invoiceFullData);
                    return [
                        'status'       => true,
                        'message'      => langx('Invoice Created'),
                        'emailResponse' => $result
                    ];
                } else { // Just Saving
                    return [
                        'status'  => true,
                        'message' => langx('Invoice Updated')
                    ];
                }
            }
        }

        return [
            'status'  => false,
            'message' => langx('Validation error found'),
            'errors' => !$this->valAsArray ? stringifyFormatErrors($val_messages) : $val_messages
        ];
    }

    public function remove($hash, $user_id) {
        //it does not remove, it only hides
        $invoice = $this->getByHash($hash, $user_id);
        if (!$invoice) { //if not exist invoice associated to user return
            return ['status' => false, 'message' => 'Invoice Not Found'];
        } else {
            if($invoice->status !== 'D')
                return ['status' => false, 'message' => 'Invoice Not Found'];
        }

        $orgnx_ids     = getOrganizationsIds($user_id);
        $orgnx_ids_arr = $orgnx_ids ? explode(',', $orgnx_ids) : [];

        $this->db->where('hash', $hash)
            ->delete($this->table);
        return ['status' => true, 'message' => 'Invoice removed'];
    }
    
    //once an invoice is set as unpaid (open) we create the pdf, 
    //it will be the pdf for that invoice eternally

    private function createPdf($invoiceFullData) {
        $hashSize = 128;
        $bytes = openssl_random_pseudo_bytes($hashSize / 2, $cstrong);
        $fileNameHash  = bin2hex($bytes);
        
        $files_location  = 'application/uploads/invoices/';
        $fileName = 'INV_' . $invoiceFullData->reference . '_' . $fileNameHash. '.pdf';
        $localPath = $files_location . $fileName;

        $this->load->model('chat_setting_model');
        $invoiceFullData->branding = $this->chat_setting_model->getChatSettingByChurch($invoiceFullData->church_id,$invoiceFullData->campus_id);

        $logo_base64 = '';        
        if($invoiceFullData->branding->logo) {//Converting image to base64 src
            $logo_path = './application/uploads/'.$invoiceFullData->branding->logo;
            $imagedata = file_get_contents($logo_path);
            $base64 = base64_encode($imagedata);
            $logo_base64 = 'data:'.mime_content_type($logo_path).';base64,'.$base64;
        }
        $invoiceFullData->branding->logo_base64 = $logo_base64;
        
        $pdf  = new Dompdf();
        $html = $this->load->view('invoice/pdf_tpl', ['view_data' => $invoiceFullData], true);
        $pdf->setPaper("Letter", "portrait");
        $pdf->loadHtml($html);
        $pdf->render();
        
        file_put_contents($localPath, $pdf->output(['compress' => 0]));
        
        $pdf_url = BASE_URL . 'files/get/invoices/' . $fileName;        
        return $pdf_url;
    }

    public function markInvoiceAs($id, $newStatus) {
        
        $nowDate = date('Y-m-d H:i:s');

        $saveData = [
            'status'     => $newStatus,
            'updated_at' => $nowDate
        ];
       
        $this->db->where('id', $id)->update($this->table, $saveData);
    }
    
    public function markInvoiceAsCanceled($id) {
        
        $client_id = $this->session->userdata('user_id');
        
        $orgnx_ids     = getOrganizationsIds($client_id);
        $this->db->where_in('church_id', explode(',', $orgnx_ids));
        
        $this->db->group_start();
        $this->db->where('status', self::INVOICE_UNPAID_STATUS);
        $this->db->or_where('status', self::INVOICE_DUE_STATUS);
        $this->db->group_end();
        
        $invoice = $this->db->where('id', $id)->get($this->table)->row();
        
        if(!$invoice) { 
            return ['status' => false, 'message' => 'Bad request'];
        }
        
        $nowDate = date('Y-m-d H:i:s');

        $saveData = [
            'status'     => self::INVOICE_CANCELED_STATUS,
            'updated_at' => $nowDate
        ];
       
        $this->db->where('id', $id)->update($this->table, $saveData);
        
        return ['status' => true, 'message' => langx('Invoice canceled'), 'message2' => langx('Reloading ...')];
    }
    
    //search invoices and set to due those invoices that have been expired
    public function setInvoicesAsDue() {

        $today = date('Y-m-d');
        $this->db->where('status', self::INVOICE_UNPAID_STATUS)
                //->where('trash', 0)
                ->where("due_date IS NOT NULL", NULL, FALSE)
                ->where("due_date <", $today)
                ->update($this->table, ['status' => 'E', 'updated_at' => date('Y-m-d H:i:s')]);

        return ['status' => true, 'affected_rows' => $this->db->affected_rows()];
    }

}
