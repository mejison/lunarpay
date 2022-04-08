<?php

function searchTemplateInPaysafeSettings($psfSettings, $tpl_code) {
    $result = ['codeParams' => null];
    foreach ($psfSettings as $currency => $currencies) {
        foreach ($currencies as $paymentMethod => $codes) {
            $paymentType = $codes['type'];
            foreach ($codes['codes'] as $code) {
                if ($code['code'] == $tpl_code) {
                    $result = ['codeParams' => $code, 'paymentType' => $paymentType, 'currency' => $currency, 'paymentMethod' => $paymentMethod];
                    break 3;
                }
            }
        }
    }

    return $result;
}

function getPaySafeFee($trx) {
    $fee = null;

    $templateArr = explode(',', preg_replace('/\s+/', '', $trx->template));

    foreach ($templateArr as $tpl_code) {
        //search product codes in live and Test, ther is no issue as codes are unique in both sides
        $codeObj = searchTemplateInPaysafeSettings(PAYSAFE_PRODUCT_CODES_LIVE, $tpl_code);
        if (!$codeObj['codeParams']) {
            $codeObj = searchTemplateInPaysafeSettings(PAYSAFE_PRODUCT_CODES_TEST, $tpl_code);
        }
        ///////////
        //we expect code params never to be null, we should always to find a a code otherwise there is a miss 
        //configuration of the organization (paysafe_template) or the paysafe settings params
        if ($codeObj['codeParams'] == null) {
            throw new Exception('Invalid paysafe settings');
        }

        //get the first CC (when cc sent) or the first BNK (when bank sent) coincidence, 
        //this is thought to work if the orgnx has 1 or 2 merchant accounts (productCodes) max, if there is more than 2 productCodes some refctoring would need to be done
        if ($trx->src == "CC" && $codeObj['paymentType'] == 'card') {
            $fee = $trx->total_amount * $codeObj['codeParams']['percent'] + $codeObj['codeParams']['const'];
            break;
        } elseif ($trx->src == "BNK" && $codeObj['paymentType'] == 'bank') {
            $fee = $trx->total_amount * $codeObj['codeParams']['percent'] + $codeObj['codeParams']['const'];
            break;
        }
    }

    $fee = round($fee, 2);
    return $fee;
}

function getPaySafeTplParams($templates) {

    $result = [];

    $templateArr = explode(',', preg_replace('/\s+/', '', $templates));

    foreach ($templateArr as $tpl_code) {

        //search product codes in live and Test, ther is no issue as codes are unique in both sides
        $codeObj = searchTemplateInPaysafeSettings(PAYSAFE_PRODUCT_CODES_LIVE, $tpl_code);
        if (!$codeObj['codeParams']) {
            $codeObj = searchTemplateInPaysafeSettings(PAYSAFE_PRODUCT_CODES_TEST, $tpl_code);
        }
        ////////////////
        //we expect code params never to be null, we should always to find a a code otherwise there is a miss configuration 
        //of the organization (paysafe_template) or the paysafe settings params
        if ($codeObj['codeParams'] == null) {
            throw new Exception('Invalid paysafe settings');
        }

        if ($codeObj['paymentType'] == 'card') {
            $result["var_cc"] = $codeObj['codeParams']['percent'];
            $result["kte_cc"] = $codeObj['codeParams']['const'];

            //do not expone productCodes outside here, just for better security
            unset($codeObj['codeParams']);

            //it may be useful in the future if we are going to use more payment methods as google play, apple, paypal, etc
            $result['objectCard'] = $codeObj;
        } elseif ($codeObj['paymentType'] == 'bank') {
            $result["var_bnk"]    = $codeObj['codeParams']['percent'];
            $result["kte_bnk"]    = $codeObj['codeParams']['const'];
            unset($codeObj['codeParams']);
            $result['objectBank'] = $codeObj;
        }
    }

    return $result;
}

function getPaysafeFreqLabel($value) {
    $options = getAllPaysafeFreqLabels();
    return isset($options[$value]) ? $options[$value] : $value;
}

function getAllPaysafeFreqLabels() {
    $options = [
        'week'      => 'Weekly',
        'month'     => 'Monthly',
        'quarterly' => 'Quarterly',
        'year'      => 'Yearly',
    ];
    return $options;
}

function getPaysafeRegions() {
    $CI                  = & get_instance();
    $paysafe_environment = $CI->config->item('paysafe_environment');

    if ($paysafe_environment === null || $paysafe_environment === 'dev') {
        $result = PAYSAFE_REGIONS_TEST;
    } else if ($paysafe_environment === 'prd') {
        $result = PAYSAFE_REGIONS_LIVE;
    } else {
        throw new Exception('Internal error, incorrect payment processor settings');
    }

    return $result;
}

function setMultiFundDistrFeeCovered($fund_data, $transactionData) {

    $fundTotalAmount = $transactionData['total_amount'] - $transactionData['fee']; //the summatory of funds conceived without fees

    foreach ($fund_data as &$fundRow) {
        $fundRow['_fund_sub_total_amount'] = $fundRow['fund_amount']; //net                    

        $perRelation          = $fundRow['fund_amount'] / $fundTotalAmount; //the fund_amount comming conceived without fee
        $fundRow['_fund_fee'] = round($transactionData['fee'] * $perRelation, 4); //discover fund fee by using the prior relation

        $fundRow['_fund_amount'] = round($fundRow['_fund_sub_total_amount'] + $fundRow['_fund_fee'], 4); //total fund amount
    }

    return $fund_data;
}

//proceed to discount fees on funds proportionally
function setMultiFundDistrFeeNotCovered($fund_data, $transactionData) {

    $fundTotalAmount = $transactionData['total_amount']; //the summatory of funds conceived with the fee

    foreach ($fund_data as &$fundRow) {

        $fundRow['_fund_amount'] = $fundRow['fund_amount']; //fund_amount (total fund amount) conceived with fee

        $perRelation          = $fundRow['fund_amount'] / $fundTotalAmount; //the fund amount comming conceived including the fee
        $fundRow['_fund_fee'] = round($transactionData['fee'] * $perRelation, 4); //discover fund fee by using the prior relation

        $fundRow['_fund_sub_total_amount'] = round($fundRow['_fund_amount'] - $fundRow['_fund_fee'], 2); //net                                        
    }

    return $fund_data;
}
