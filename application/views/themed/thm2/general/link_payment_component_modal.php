
<div id="link-payment-modal-component">
    <style>
        .modal-full-screen .modal-dialog {
            width: 100%!important; height: 100%!important;margin: 0!important; padding: 0!important;

        }
        @media (min-width: 576px){
            .modal-full-screen .modal-dialog {
                max-width: 100%!important;
                margin: 0px!important;
            }
        }

        .modal-full-screen .modal-content {
            height: 100%!important; 
            min-height: 100%!important; border-radius: 0!important;
        }

        .modal-full-screen .modal .modal-body {
            overflow-y: auto;
        }

        .modal-full-screen .modal {
            padding: 0 !important; 
        }

        .modal-full-screen .modal-header .close {
            float:left!important;
        }

        .modal-full-screen  .close > span:not(.sr-only) {
            font-size: 1.75rem;
        }

        .modal-full-screen .modal-header .close {
            margin: -1.1rem
        }

        .modal-full-screen .modal-footer {
            padding-bottom: 50px
        }

        #products-list .btn-add-product {
            display: none;
        }

        #products-list  .product-row:last-child .btn-add-product {
            display: block !important;
        }

        #products-list .product-row:only-child .remove-product-row-btn {
            display: none !important;
        }
        table  td {
            text-align:left!important
        }
    </style>
    <div class="modal fade modal-full-screen" id="main_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="overflow-y: auto">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </span>
                        &nbsp;| &nbsp; 
                        <span id="component_title"> <?= langx('create_payment_link') ?> </span>
                        <span style="font-size: 12.7px; padding-top: 20px; line-height: 24px; font-weight: normal; font-style: italic; display: none" class="subtitle">
                            <br>
                            <span class="organization_name" style="font-weight: bold;display: block;margin-left: 53px;"></span>
                        </span>
                    </h4>
                    <div class="float-right">
                        <button type="button" class="btn btn-primary m-auto btn-save" style="width: 150px; margin-left: 10px !important;">Create Link</button>
                    </div>
                </div>
                <div class="modal-body">                
                    <?php echo form_open("invoice/create", ['role' => 'form', 'id' => 'main_form']); ?>
                    <div class="row">
                        
                        <div class="col-md-12 p-5">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <strong><?php echo langx('payment_options', 'payment_options'); ?> <br /></strong>
                                        <select class="form-control select2 payment_options" name="payment_options" >
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-0 mb-1">   
                            <div id="products-list"></div>
                        </div>
                       <!-- <div class="col-md-5 just-dev">
                            <div class="ml-1 mt-4">
                                <div class="mt-3">
                                    Adjust your brand settings in <strong><a class="text-white" target="_BLANK" href="<?= BASE_URL ?>settings/branding">branding page</a></strong>                                
                                </div>
                                <div class="mt-4 mb">
                                    <strong><label for="memo">Email preview</label></strong>
                                </div>                            
                            </div>
                            <?php
                                /*$invoice_html = $this->load->view("email/invoice.html",'',true);
                                $invoice_html = str_replace("[baseUrl]", CUSTOMER_APP_BASE_URL, $invoice_html);
                                $invoice_html = str_replace("[baseAssets]", BASE_ASSETS, $invoice_html);
                                $invoice_html = str_replace("[PaymentLink]", '#', $invoice_html);
                                $invoice_html = str_replace("[link_pdf]", '#', $invoice_html);
                                $invoice_html = str_replace("[products]", '', $invoice_html);
                                $invoice_html = str_replace("[OrgName]",
                                    '<tr><td><span style="color: #7A7A7A;line-height: 1.625; font-weight: 400; margin-top: 0; margin-bottom: 1rem;">From: <span class="email_organization" style="color:#1A1A1A"></span></span></td></tr>'
                                    , $invoice_html);
                                echo $invoice_html;*/
                            ?>
                        </div> -->
                    </div>
                    <?php echo form_close(); ?>
                </div>
                <div class="modal-footer text-center">
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>    