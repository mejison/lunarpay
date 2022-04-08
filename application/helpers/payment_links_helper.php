<?php

// --- We need to ensure the client sends products that belongs to the created payment link, and to quantities are available
// --- It will just throw an exception if it found some malicious or not well formed request
function PL_checkProductsIntegrity($paymentLink, $reqProducts) {
    foreach ($reqProducts as $reqProd) {
        $found         = false;
        $quantityCheck = false;
        foreach ($paymentLink->products as $linkProdOrigin) {
            if ($reqProd->link_product_id == $linkProdOrigin->id) { // --- product belongs
                $found = true;
                if ($reqProd->qty <= $linkProdOrigin->qty) { // --- we need to check the customer is not passing the max limit for that product on that payment link
                    $quantityCheck = true;
                }
                break;
            }
        }
        if (!$found) {
            throw new Exception(langx('Products integrity checks not passed'));
        }

        if (!$quantityCheck) {
            throw new Exception(langx('Products quantity checks not passed'));
        }
    }
}

// --- Calculate the total amount using quantities send by the customer and include the origninal product data base
function PL_recalcProductsWithRequest($reqProducts) {
    $CI     = & get_instance();    
    $CI->load->model('payment_link_product_model');
    $totalAmount = 0;
    
    $productsWithRequest = [];
    
    foreach ($reqProducts as $reqProd) {
        $linkProdOrigin = $CI->payment_link_product_model->get($reqProd->link_product_id);
        $linkProdOrigin->_qty_req = $reqProd->qty;
        
        $productsWithRequest [] = $linkProdOrigin;                
        
        $totalAmount   += $linkProdOrigin->product_price * $reqProd->qty;
    }

    return ['totalAmount' => $totalAmount, '_products' => $productsWithRequest];
}
