<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'core/' . 'MY_Customer.php';

class Invoice extends My_Customer {
    public function __construct() {
        parent::__construct();

        $this->template_data['view_index'] = $this->router->fetch_class() . '/invoice';// . $this->router->fetch_method();
        $this->load->use_theme('themed/thm2-customer/');
        $this->load->library(['form_validation']);
    }
    
    public function index($invoice_hash, $walletAddress = '') {
        $data = ['hash' => $invoice_hash, 'walletAddr' => $walletAddress];
        $view = $this->load->view('/invoice', ['view_data' => $data], true);  
        $this->template_data['content'] = $view;
        $this->load->view('layout', $this->template_data);
    }

    public function apiLogs($hash)
    {
        $this->load->model('invoice_model');
        $this->invoice_model->valAsArray = true;
        $invoice = $this->invoice_model->getByHash($hash);
        if (empty($invoice)) {
            output_json_api(['errors' => [langx('Invoice not found')]], 1, REST_Controller_Codes::HTTP_OK);
            return;
        }
        if ($invoice->status == Invoice_model::INVOICE_PAID_STATUS) {
            output_json_api(['errors' => [langx('Invoice already paid')]], 1, REST_Controller_Codes::HTTP_OK);
            return;
        }

        echo '<pre>';
        echo "----------Invoice Data----------";
        echo "<br>";
        echo "<br>";
        print_r($invoice);
        echo "<br>";
        echo "<br>";
        echo "----------Invoice Data End----------";
        echo "<br>";
        echo "<br>";

        //staging
        $apiKey = 'f54965c2-998b-4f5a-854d-bc1b9b4935d7';
        $apiSecret = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJBUElfS0VZIjoiZjU0OTY1YzItOTk4Yi00ZjVhLTg1NGQtYmMxYjliNDkzNWQ3IiwiaWF0IjoxNjQ4MDEyODIzfQ.zErwPftvGZRCEfgMmpcLs886mu-XUyeGTqAAkOP-lbY';

        //Get fiat currencies

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://staging-api.transak.com/api/v2/currencies/fiat-currencies",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "api-key: $apiKey"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $res = [];

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            foreach (json_decode($response, true) as $d) {
                foreach ($d as $index => $arr) {
                    unset($arr['icon']);
                    if ($arr['symbol'] == 'USD') {
                        $res[] = $arr;
                    }
                }
            }
        }

        echo "----------for getting payment option (call fiat-currencies)----------";
        echo "<br>";
        echo "<br>";
        print_r($res);
        echo "<br>";
        echo "<br>";
        echo "----------End fiat-currencies----------";
        echo "<br>";
        echo "<br>";
        echo "<br>";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://staging-api.transak.com/api/v2/currencies/price?fiatCurrency=USD&cryptoCurrency=ETH&isBuyOrSell=BUY&paymentMethod=credit_debit_card&fiatAmount=250&network=ethereum",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "api-key: $apiKey"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $res = [];

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $res = json_decode($response, true);
        }

        echo "----------Get a currency price----------";
        echo "<br>";
        echo "<br>";
        print_r($res);
        echo "<br>";
        echo "<br>";
        echo "----------End Get a currency price----------";
        echo "<br>";
        echo "<br>";
        echo "<br>";


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://staging-api.transak.com/api/v2/currencies/verify-wallet-address?walletAddress=0x2dd94DC4b658F08E33272e6563dAb1758c10b1de&cryptoCurrency=ETH&network=ethereum",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "api-key: $apiKey"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $res = [];

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $res = json_decode($response, true);
        }

        echo "----------Enter Wallet Address and it gets verified----------";
        echo "<br>";
        echo "<br>";
        echo "Wallet Address staging :=> 0x2dd94DC4b658F08E33272e6563dAb1758c10b1de";
        echo "<br>";
        echo "<br>";
        echo "response = 1 means it's verified.!";
        echo "<br>";
        echo "<br>";
        print_r($res);
        echo "<br>";
        echo "<br>";
        echo "----------End  Wallet Address and it gets verified----------";
        echo "<br>";
        echo "<br>";
        echo "<br>";


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://staging-api.transak.com/api/v2/partners/webhooks?partnerAPISecret=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJBUElfS0VZIjoiYmE4Y2YxNTgtZDVkNy00OWQzLTk5ZTgtZGU3NDlkMjJjNmZlIiwiaWF0IjoxNjQ3OTQxMjIwfQ.E7R9Vgg9axj0eGFBTkH1ovUfOOdRwqUiCFmhlzEC0E4",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "api-key: $apiKey"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $res = [];

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $res = json_decode($response, true);
        }

        echo "----------Get getWebhooks----------";
        echo "<br>";
        echo "<br>";
        print_r($res);
        echo "<br>";
        echo "<br>";
        echo "----------End getWebhooks----------";
        echo "<br>";
        echo "<br>";
        echo "<br>";
    }
}
