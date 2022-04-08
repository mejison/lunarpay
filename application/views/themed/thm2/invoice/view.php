<style>
    .card-stats .card-body {
        min-height: 120px;
    }
    .d-block{
        font-size: .9rem !important; /*improvex*/
        font-weight: 500; /*improvex*/
    }
    
    /*custom style for invoice status*/
  .invoiceStatus span.badge {font-size: .84em; width: auto!important; padding: 6px 10px; margin-left: -4px;}
  
</style>
<!-- Header-->
<!-- Header -->
<div class="header pb-6 d-flex align-items-center" style="min-height: 146px; background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8" style="background-color: inherit!important; background: inherit!important"></span>
    <!-- Header container -->
    <div class="container-fluid align-items-center">
        <div class="row">
            <div class="col-lg-7 col-md-10">
                <h1 class="display-2"></h1>
                <p class="mt-0 mb-5" style="margin:0!important; margin-top: -10px!important"></p>
            </div>
        </div>
    </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="row" >
        <div class="col-xl-12 order-xl-1 align-items-center">
            <div class="row justify-content-center">
                <!--<div class="col-lg-2"></div>-->
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center"> 
                                <div class="col-md-3">
                                    <h3 class="mb-0">Invoice</h3>
                                </div>
                                <div class="col-md-9 text-right">
                                    <button   class="btn btn-neutral btn-sm btn-cancel-invoice float-right top-table-bottom m-2 <?= (!in_array($invoice->status, [Invoice_model::INVOICE_UNPAID_STATUS, Invoice_model::INVOICE_DUE_STATUS])) ? "d-none" : "" ?>" data-id="<?= $invoice->id ?>"  type="button">
                                        <i class="fas fa-ban"></i> <span class="btn-inner--text">Cancel </span>
                                    </button>
                                    <button   class="btn btn-neutral btn-sm btn-clone-invoice float-right top-table-bottom m-2" data-id="<?= $invoice->id ?>"  type="button">
                                        <i class="fas fa-clone"></i> <span class="btn-inner--text">Clone</span>
                                    </button>
                                    <button onclick="window.location = '<?= $invoice->pdf_url ?>'"   class="btn btn-neutral btn-sm float-right top-table-bottom m-2 <?= (is_null($invoice->pdf_url)) ? "d-none" : "" ?> "  type="button">
                                        <span class="btn-inner--text"><i class="fas fa-file-pdf"></i> Download PDF</span>
                                    </button>
                                    <button   class="btn btn-neutral btn-sm btn-send-invoice float-right top-table-bottom m-2 <?= (!in_array($invoice->status, [Invoice_model::INVOICE_UNPAID_STATUS, Invoice_model::INVOICE_DUE_STATUS])) ? "d-none" : "" ?>" data-hash="<?= $invoice->hash ?>"  type="button">
                                        <span class="btn-inner--text">Send Invoice </span>
                                    </button>
                                    <button  class="btn btn-neutral btn-sm btn-copy-invoice float-right top-table-bottom m-2 <?= (!in_array($invoice->status, [Invoice_model::INVOICE_UNPAID_STATUS, Invoice_model::INVOICE_DUE_STATUS])) ? "d-none" : "" ?>" data-link="<?= CUSTOMER_APP_BASE_URL . 'c/invoice/' . $invoice->hash ?>"  type="button">
                                        <span class="btn-inner--text"><i class="fas fa-clipboard"></i> Copy Link</span>
                                    </button>
                                    <button onclick="window.open('<?= CUSTOMER_APP_BASE_URL . 'c/invoice/' . $invoice->hash . '/' . Invoice_model::METAMASK_ADDRESS ?>', '_blank').focus();"  class="btn btn-neutral btn-sm float-right top-table-bottom m-2 <?= (!in_array($invoice->status, [Invoice_model::INVOICE_UNPAID_STATUS, Invoice_model::INVOICE_DUE_STATUS])) ? "d-none" : "" ?>"    type="button">
                                        <span class="btn-inner--text">View</span>
                                    </button>                                        
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="padding-left: 60px">
                           
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">                                        
                                        <div class="col-lg-12 text-left invoiceStatus">
                                            <h6 class="heading-small mb-1 pb-2"><?=$invoice->_statusHtml?></h6>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Invoice Number</span>
                                                <span class="d-block h3"> <?=$invoice->reference?></span>
                                            </div>
                                        </div>                                        
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Billed to</span>
                                                <span class="d-block h3"><?=$invoice->customer->email?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Customer</span>
                                                <span class="d-block h3"><?=$invoice->customer ? $invoice->customer->first_name.' '.$invoice->customer->last_name : ''?>
                                             </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Total</span>
                                                <span class="d-block h3"><strong>$<?=number_format($invoice->total_amount + $invoice->fee, 2, '.', ',') ?></strong></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Subtotal</span>
                                                <span class="d-block h3">
                                                    $<?= number_format($invoice->total_amount, 2, '.', ',') ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Fee Covered</span>
                                                <span class="d-block h3">
                                                    <?= $invoice->cover_fee ? '$' . number_format($invoice->fee, 2, '.', ',') : 'No' ?>
                                                </span>
                                            </div>
                                        </div>                                        
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Created</span>
                                                <span class="d-block h3"><?= ($invoice->created_at) ?   date("F j, Y",strtotime($invoice->created_at)) : ' - ' ?></span>
                                            </div>
                                        </div>                                        
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Finalized</span>
                                                <span class="d-block h3"><?= ($invoice->finalized) ? date("F j, Y", strtotime($invoice->finalized)) : ' - ' ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Due date</span>
                                                <span class="d-block h3"><?= ($invoice->due_date) ?   date("F j, Y",strtotime($invoice->due_date)) : ' - ' ?></span>
                                            </div>
                                        </div>                                        
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Payment options</span>
                                                <span class="d-block h3">
                                                    <?= (in_array('CC', json_decode($invoice->payment_options, true)) && in_array('BANK', json_decode($invoice->payment_options, true))) ? 'Credit Card - Bank' : (in_array('CC', json_decode($invoice->payment_options, true)) ? 'Credit Card' : 'Bank') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Memo</span>
                                                <span class="d-block h3"><?= ($invoice->memo) ? $invoice->memo : ' - ' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                            <?php echo form_open("", ['role' => 'form', 'id' => 'token_form']); ?>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>        
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h3 class="mb-0">Products</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" style="padding-left: 60px">
                                <div class="row">
                                <div class="col-lg-4 h6 surtitle text-light text-muted">Product name</div>
                                <div class="col-lg-4 h6 surtitle text-light text-muted ">Qty</div>
                                <div class="col-lg-2 h6 surtitle text-light text-muted text-right">Price</div>
                                </div>
                                <?php $sum=0; foreach ($invoice->products as $product) { $sum= $sum+($product->quantity*$product->product_inv_price); ?>
                                    <div class="row">
                                        <div class="col-lg-4">
                                                <div class="form-group">
                                                    <span class="d-block h4 mb-0"><?= $product->product_inv_name?></span>
                                                    <?= $product->digital_content ? '<a class="text-xs" href="'.$product->digital_content_url.'">Download Deliverable</a>' : '' ?>
                                                </div>
                                        </div>
                                        <div class="col-lg-4">
                                                <div class="form-group">
                                                    <span class="d-block h4 mb-0"><?= $product->quantity?></span>
                                                </div>
                                        </div>
                                        <div class="col-lg-2">
                                                <div class="form-group text-right">
                                                    <span class="d-block h3">$<?= number_format($product->product_inv_price, 2, '.', ',')?></span>
                                                </div>
                                        </div>
                                    </div>
                                <?php };?>
                                <?php if($invoice->cover_fee): ?>                                    
                                    <div class="row">
                                        <div class="col-lg-4">
                                                <div class="form-group">
                                                    <span class="d-block h4 mb-0">Processing Fee</span>                                                    
                                                </div>
                                        </div>
                                        <div class="col-lg-4">
                                                <div class="form-group">                                                    
                                                </div>
                                        </div>
                                        <div class="col-lg-2">
                                                <div class="form-group text-right">
                                                    <span class="d-block h3">$<?= number_format($invoice->fee, 2, '.', ',')?></span>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12"><hr class="mt-0 mb-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4"><span class="h6 surtitle text-light text-muted text">Subtotal</span></div>
                                        <div class="col-lg-4">
                                                <div class="form-group">                                                    
                                                </div>
                                        </div>
                                        <div class="col-lg-2">
                                                <div class="form-group text-right">
                                                    <span class="d-block h3">$<?= number_format($invoice->total_amount, 2, '.', ',')?></span>
                                                </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                    
                                    <div class="row">
                                    <div class="col-lg-4"><span class="h6 surtitle text-light text-muted">Total</span></div>
                                    <div class="col-lg-4"><span class="h4  text-light">&nbsp;</span></div>
                                    <div class="col-lg-2"><span class="d-block h3 text-right font-weight-bold"> $<?=number_format($sum + $invoice->fee, 2, '.', ',')?></span></div>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>  
            <?php if(count($invoice->payments) != 0) :?>
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <h3 class="mb-0">Payments</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" style="padding-left: 60px">
                                        <?php $sum=0; foreach ($invoice->payments as $payment) :  ?>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <span class="h6 surtitle text-light text-muted">Transaction Id</span>
                                                        <span class="d-block h3"><?=$payment->paysafeRef?></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <span class="h6 surtitle text-light text-muted">Type</span>
                                                        <span class="d-block h3"><?=$payment->src?></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <span class="h6 surtitle text-light text-muted">Created At</span>
                                                        <span class="d-block h3"><?=date("F j, Y",strtotime($payment->created_at)) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <span class="h6 surtitle text-light text-muted">Sub Total</span>
                                                        <span class="d-block h3">$<?= number_format($payment->total_amount, 2, '.', ',')?></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <span class="h6 surtitle text-light text-muted">Fee</span>
                                                        <span class="d-block h3">$<?=$payment->fee?></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <span class="h6 surtitle text-light text-muted">Total</span>
                                                        <span class="d-block h3 font-weight-bold">$<?=number_format($payment->total_amount, 2, '.', ',')  ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row border-top">&nbsp;</div>
                                        <?php endforeach;?>
                                    </div>
                                </div>
                    </div>
                </div>
                <?php endif;?>
        </div>        
    </div>
</div>
 