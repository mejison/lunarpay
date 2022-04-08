<?php $this->load->view('ui_loader') ?>
<?php
$walletAddr = '';
if (isset($view_data['walletAddr']) && $view_data['walletAddr'] != '') {
    $walletAddr = $view_data['walletAddr'];
}
?>
<style>
    .gray_label {
        color: #7a7a7a;
    }

    .black_label {
        color: black;
    }
</style>
<style id="css_branding"></style>
<div class="container-fluid theme_background_color">
    <div class="row" id="form_details">
        <div class="col-lg-4"></div>
        <div class="col-lg-4 mt-4">
            <div class="App-Payment rounded Tabs is-icontabs is-desktop px-5">
                <div class="Tabs-Container">
                    <div class="row">
                        <div class="col-9 pt-3">
                            <img style="max-height: 50px; display: block; margin-left: -25px; margin-bottom: 8px;" id="invoice_logo" src="" />
                            <span id="invoice_total" class="total black_label"></span><br>
                            <span id="invoice_due_date" class="due_date"></span><br><br>
                        </div>
                        <div class="col-3 pt-1 d-flex justify-content-end" style="margin-top: 15px;">
                            <i class="fas fa-file-invoice theme_foreground_color" style="font-size: 60px; "></i>
                        </div>
                    </div>
                    <div class="row pb-1 mb-2">
                        <table style="width:100%;margin-left:15px;">
                            <tr>
                                <td width="25%"><span class="toFrom gray_label">To</span></td>
                                <td><span id="customer_name" class="customer_name"></span></td>
                            </tr>
                            <tr>
                                <td><span class="toFrom gray_label">From</span></td>
                                <td><span id="orgSub_name" class="customer_name"> - </span></td>
                            </tr>
                            <tr>
                                <td><span class="toFrom gray_label">Memo</span></td>
                                <td><span id="orgSub_name" class="customer_memo due_date font-weight-normal"> - </span></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="pt-3">
                                    <span class="toFrom">
                                        <u>
                                            <a href="javascript:void(0)" class="button" id="Invoice-downloadButton"> Download PDF <i class="fas fa-arrow-down"></i></a>
                                        </u>
                                    </span>
                                </td>
                            </tr>



                        </table>
                    </div>
                    <div class="row  border-top ml-0" style="height:10px">&nbsp;</div>
                    <div class="row ">
                        <div class="col pb-4">
                            <button data-toggle="collapse" data-target="#collapsibleDetails" style="cursor:pointer" class="Button ViewInvoiceDetailsLink Button--link" type="button">
                                <div class="flex-container justify-content-center align-items-center">
                                    <svg class="InlineSVG Icon Button-Icon Button-Icon--right Icon--sm Icon--square" focusable="false" fill-opacity="1" fill="currentColor" width="12" height="12" viewBox="0 0 5 8">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.146582 1.20432C-0.0488607 1.00888 -0.0488607 0.692001 0.146582 0.496558C0.342025 0.301115 0.6589 0.301115 0.854343 0.496558L4.00421 3.64642C4.19947 3.84168 4.19947 4.15827 4.00421 4.35353L0.854343 7.50339C0.6589 7.69884 0.342025 7.69884 0.146582 7.50339C-0.0488607 7.30795 -0.0488607 6.99108 0.146582 6.79563L2.94224 3.99998L0.146582 1.20432Z"></path>
                                    </svg>
                                    <span class="due_date italic">Show Invoice Details</span>
                                </div>
                            </button>
                            <div id="collapsibleDetails" class="collapse">
                                <div class="spacing-4 direction-column mt-1">
                                    <span class="due_date"></span> <span class="Text toFrom due_date" id="invoice_"></span>
                                </div>
                                <table cellpadding="5" cellspacing="5" style="width: 100%;padding:5px" id="product_details">
                                    <tbody>
                                        <tr>
                                            <td height="20" colspan="2"></td>
                                        </tr>
                                        <tr id="detail">
                                            <td class="col-spacer" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td height="20" colspan="2"></td>
                                        </tr>

                                        <tr>
                                            <td colspan="2" class="line2"></td>
                                        </tr>
                                        <tr>
                                            <td height="10" colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="customer_name">Amount due</span>
                                            </td>
                                            <td height="5">
                                                <span class="span-amount"><strong id="total_invoice"></strong></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">&nbsp;</div>
    <!--form pago -->
    <div class="row" id="form_payment">
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <div class="App-Payment rounded px-5 pb-2 PaymentFormFixedHeightContainer flex-container direction-column">
                <form novalidate="" id="#payment-form" style="width: 100%">
                    <input type="hidden" id="hidden_wallet_address" value="<?= $walletAddr; ?>">
                    <div style="display: inherit; height: 70px;">
                        <div>
                            <div class="Divider">
                                <hr>
                                <h6 class="Divider-Text Text customer_name">
                                    Payment form
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="App-Global-Fields flex-container spacing-16 direction-row wrap-wrap">
                    </div>
                    <div class="Tabs is-icontabs is-desktop">
                        <div class="Tabs-Container">
                            <div id="payments-options"></div>
                        </div>
                        <div class="Tabs-TabPanelContainer">
                            <div style="width: 100%; transform: none;">
                                <div id="card-tab-panel" role="tabpanel" aria-labelledby="card-tab" style="display: none;">
                                    <!--card panel-->
                                </div>
                                <div id="ach-tab-panel" role="tabpanel" style="display: none;"></div>
                                <div id="crypto-tab-panel" role="tabpanel" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-item width-grow mt-3" style="text-align: center;display: none;" id="w-connect-div">
                        <a href="javascript:void(0)">
                            <img src="<?= BASE_ASSETS; ?>images/walletconnect.jpg" style="width: 250px; height: 50px;" />
                        </a>
                    </div>
                    <div class="flex-item width-grow mt-3" style="text-align: center;display: none;" id="m-connect-div">
                        <a href="javascript:void(0)">
                            <img src="<?= BASE_ASSETS; ?>images/metamask.png" style="width: 250px; height: 50px;" />
                        </a>
                    </div>
                    <div class="flex-item width-grow mt-3" style="text-align: center;display: none;" id="w-address-div">
                        <input class="CheckoutInput" placeholder="WalletAddress" value="test@tet.com" id="w-address" disabled />
                    </div>
                    <div class="flex-item width-grow mt-3" style="text-align: center;display: none;" id="wc-div">
                        <a style="color:whitesmoke !important" class="btn btn-sm btn-default mb-0 theme_color text_theme_color" style="border: none !important;" id="pay-wc-btn">Pay with Wallect-Connect</a>
                    </div>
                    <div class="PaymentForm-confirmPaymentContainer flex-item width-grow mt-2">
                        <div class="flex-item width-12"></div>
                        <div class="flex-item width-12">
                            <button class="SubmitButton btn btn-sm btn-default mb-0 theme_color text_theme_color" style="border: none !important;" id="pay-button" type="submit">
                                <div class="SubmitButton-Shimmer" style="background-image: linear-gradient(to right, rgba(0, 116, 212, 0) 0%, rgb(58, 139, 238) 50%, rgba(0, 116, 212, 0) 100%);"></div>
                                <div class="SubmitButton-TextContainer">
                                    <span class="SubmitButton-Text SubmitButton-Text--current Text Text-color--default Text-fontWeight--500 Text--truncate" aria-hidden="false">Loading ...</span>
                                    <span class="SubmitButton-Text SubmitButton-Text--pre Text Text-color--default Text-fontWeight--500 Text--truncate" aria-hidden="true">Processing...</span>
                                </div>
                                <div class="SubmitButton-IconContainer">
                                    <div class="SubmitButton-Icon SubmitButton-Icon--pre">
                                        <div class="Icon Icon--md Icon--square">
                                            <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" focusable="false">
                                                <path d="M3 7V5a5 5 0 1 1 10 0v2h.5a1 1 0 0 1 1 1v6a2 2 0 0 1-2 2h-9a2 2 0 0 1-2-2V8a1 1 0 0 1 1-1zm5 2.5a1 1 0 0 0-1 1v2a1 1 0 0 0 2 0v-2a1 1 0 0 0-1-1zM11 7V5a3 3 0 1 0-6 0v2z" fill="#ffffff" fill-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="SubmitButton-Icon SubmitButton-SpinnerIcon SubmitButton-Icon--pre">
                                        <div class="Icon Icon--md Icon--square">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" focusable="false">
                                                <ellipse cx="12" cy="12" rx="10" ry="10" style="stroke: rgb(255, 255, 255);"></ellipse>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="SubmitButton-CheckmarkIcon">
                                    <div class="Icon Icon--md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="14" focusable="false">
                                            <path d="M 0.5 6 L 8 13.5 L 21.5 0" fill="transparent" stroke-width="2" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                </div>
                            </button>
                            <div class="ConfirmPayment-PostSubmit" style="min-height: 60px">
                                <div class="row" style="display:none;">
                                    <div class="col-xs-12">
                                        <p class="payment-errors Text-fontSize--14"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>