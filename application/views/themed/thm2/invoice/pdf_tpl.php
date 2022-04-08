<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
     
    body {
        font-family: Helvetica !important;
        font-size: 13px;
    }       
        
    .invoice-box {        
        max-width: 800px;
        margin: auto;
        padding: 0px 30px 30px 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);        
        line-height: 24px;
        color: #555;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(4) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 10px;
    }

    .invoice-box table tr.heading  {
       
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    .invoice-box table tr.head td{
        border-top: 1px solid #000;
    }
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(n+2):nth-child(-n+4) {
        border-top: 1px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    .rtl {
        direction: rtl;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(4) {
        text-align: left;
    }
    footer {
                position: fixed; 
                bottom: -40px; 
                left: 0px; 
                right: 0px;
                height: 150px; 
                border-top: 1px solid #eee;
                background-color: #fff;
                color: #333;
                text-align: left;
                line-height: 21px;
            }
    @page {
        margin: 30px 25px;
    }
    .wrapper-page {
        page-break-after: always;
    }

    .wrapper-page:last-child {
        page-break-after: avoid;
    }
    
    .invoice-box .memo {
        padding-top: 20px;
        padding-bottom: 20px;
    }
    </style>
</head>

<body>
<div class="wrapper-page">
    <footer>
        <span>
            <?= $view_data->footer ? $view_data->footer . '<br><br>' : '' ?> 
            Pay Invoice <?=$view_data->reference?> <a href="<?=CUSTOMER_APP_BASE_URL.'c/invoice/'.$view_data->hash?>">Here</a>
            <br>            
            Invoicing brought to you by <a href="http://lunarpay.com"><?= COMPANY_SITE ?></a>
        </span>
    </footer>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="4"><h3>INVOICE</h3><td>
            </tr>
                
            <tr class="top">                
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <img width="135" src="<?= $view_data->branding->logo_base64 ?>" alt="" />
                            </td>                            
                            <td>
                                <style>
                                    .lc1 { width:48%; float:left; text-align: right } 
                                    .lc2 { width:50%; float:right; text-align: right }
                                </style>
                                <div class="lc1">Invoice Reference:</div>
                                <div class="lc2"><strong><?=$view_data->reference?></strong></div>
                                <div style="clear:both"></div>                                
                                <div class="lc1">Date of issue:</div>
                                <div class="lc2"><?=date("F j, Y",strtotime($view_data->finalized)) ?></div>
                                <div style="clear: both"></div>                                
                                <div class="lc1">Due date:</div>
                                <div class="lc2"><?=date("F j, Y",strtotime($view_data->due_date)) ?></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <?=$view_data->organization->name?><br>
                                <?=$view_data->organization->street_address?><br>
                                <?=$view_data->organization->state?>
                            </td>
                            <td></td>
                            <td></td>
                            <td>
                                <strong>Billed To</strong><br>
                                <?=$view_data->customer->first_name?> <?=$view_data->customer->last_name?><br>
                                <?=$view_data->customer->email?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4"><a href="<?=CUSTOMER_APP_BASE_URL.'c/invoice/'.$view_data->hash?>">Pay online </a></td>
            </tr>
            <?php if($view_data->memo) : ?>
                <tr>
                    <td colspan="4" class="memo"><?=$view_data->memo?></td>
                </tr>
            <?php endif; ?>
            <tr class="heading">
                <td>
                    Description
                </td>
                <td>
                    Qty
                </td>
                <td>
                    Unit price
                </td>
                <td>
                    Amount
                </td>
            </tr>
            <tr class="head"><td colspan="4"></td></tr>
            <?php $sum=0;$i=0; foreach ($view_data->products as $product) :  $sum= $sum+($product->quantity*$product->product_inv_price); ?>
                <tr class="item">
                    <td>
                        <?= $product->product_inv_name?>
                    </td>
                    <td>
                        <?= $product->quantity?>
                    </td>
                    <td>
                        $<?=number_format($product->product_inv_price, 2, '.', ',')?>
                    </td>
                    <td>
                        $<?=number_format(($product->quantity*$product->product_inv_price), 2, '.', ',')?>
                    </td>
                </tr>
            <?php $i+=1; endforeach;?>

            <?php if($view_data->cover_fee) : ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Subtotal</td>
                    <td>$<?= number_format($sum, 2, '.', ',') ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Processing Fee</td>
                    <td>$<?= number_format($view_data->fee, 2, '.', ',') ?></td>
                </tr>
            <?php endif; ?>
            
            <tr class="total">
                <td></td>
                <td style="border-top: 1px solid #eee;"></td>
                <td style="border-top: 1px solid #eee;"><strong>Amount due</strong></td>
                <td style="border-top: 1px solid #eee;">
                    <strong>$<?=number_format($sum + $view_data->fee, 2, '.', ',')?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            
        </table>
    </div>
</div>
</body>
</html>