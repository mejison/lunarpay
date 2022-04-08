<div id="invoice-component">
    <style>
        label {
            display: inline-block;
            margin-bottom: .5rem;
        }

        .modal-full-screen .modal-dialog {
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;

        }

        @media (min-width: 576px) {
            .modal-full-screen .modal-dialog {
                max-width: 100% !important;
                margin: 0px !important;
            }
        }

        .modal-full-screen .modal-content {
            height: 100% !important;
            min-height: 100% !important;
            border-radius: 0 !important;
        }

        .modal-full-screen .modal .modal-body {
            overflow-y: auto;
        }

        .modal-full-screen .modal {
            padding: 0 !important;
        }

        .modal-full-screen .modal-header .close {
            float: left !important;
        }

        .modal-full-screen .close>span:not(.sr-only) {
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

        #products-list .product-row:last-child .btn-add-product {
            display: block !important;
        }

        #products-list .product-row:only-child .remove-product-row-btn {
            display: none !important;
        }
    </style>

    <style id="css_preview"></style>

    <div class="modal fade modal-full-screen" id="main_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="overflow-y: auto">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <span type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </span>
                        &nbsp;| &nbsp;
                        <span id="component_title"> <?= langx('create_invoice') ?> </span>
                        <span style="font-size: 12.7px; padding-top: 20px; line-height: 24px; font-weight: normal; font-style: italic; display: none" class="subtitle">
                            <br>
                            <span class="organization_name" style="font-weight: bold;display: block;margin-left: 53px;"></span>
                        </span>
                    </h4>
                    <div class="float-right">
                        <button type="button" class="btn btn-neutral m-auto btn-save" style="width: 150px">Save Draft</button>
                        <button type="button" class="btn btn-primary m-auto btn-review" style="width: 150px; margin-left: 10px !important;">Review Invoice</button>
                    </div>
                </div>
                <div class="modal-body">


                    <?php echo form_open("invoice/create", ['role' => 'form', 'id' => 'main_form']); ?>

                    <div class="row">
                        <div id="initial_space" class="col-md-1"></div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong><?php echo langx('customer', 'account_donor_id'); ?> <br /></strong>
                                        <select class="form-control select2 donor" name="account_donor_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong><?php echo langx('payment_options', 'payment_options'); ?> <br /></strong>
                                        <select class="form-control select2 payment_options" name="payment_options">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-0 mb-1">
                            <div id="products-list"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong><?php echo langx('memo', 'memo'); ?> <br /></strong>
                                        <textarea class="form-control" name="memo" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <strong><?php echo langx('due_date', 'due_date'); ?> <br /></strong>
                                        <input id="due_date" name="due_date" class="form-control" data-provide="datepicker" data-date-format="mm/dd/yyyy" data-date-start-date="0d">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong><?php echo langx('footer', 'footer'); ?> <br /></strong>
                                        <textarea class="form-control" placeholder="It will show up on the PDF invoice only" name="footer" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5" style="/*box-shadow: -6px 3px 13px -6px #cbcbcb;*/">
                            <div class="ml-1 mt-4">
                                <div class="mt-3">
                                    Adjust your brand settings in <strong><a class="text-white" target="_BLANK" href="<?= BASE_URL ?>settings/branding">branding page</a></strong>
                                </div>
                                <div class="mt-4 mb">
                                    <strong><label for="memo">Email preview</label></strong>
                                </div>
                            </div>
                            <?php
                            $invoice_html = $this->load->view("email/invoice.html", '', true);
                            $invoice_html = str_replace("[baseUrl]", CUSTOMER_APP_BASE_URL, $invoice_html);
                            $invoice_html = str_replace("[logoUrl]", '', $invoice_html);
                            $invoice_html = str_replace("[hasLogo]", 'block', $invoice_html);
                            $invoice_html = str_replace("[CompanySite]", COMPANY_SITE, $invoice_html);
                            $invoice_html = str_replace("[baseAssets]", BASE_ASSETS, $invoice_html);
                            $invoice_html = str_replace("[PaymentLink]", '#', $invoice_html);
                            $invoice_html = str_replace("[link_pdf]", '#', $invoice_html);
                            $invoice_html = str_replace("[products]", '', $invoice_html);
                            $invoice_html = str_replace("[ThemeColor]", '', $invoice_html);
                            $invoice_html = str_replace("[BackColor]", '', $invoice_html);
                            $invoice_html = str_replace("[ForeColor]", '', $invoice_html);
                            $invoice_html = str_replace("[OrgName]", '', $invoice_html);
                            echo $invoice_html;
                            ?>
                        </div>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="reviewModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><b id="review-invoice-data"></b></p>
                    <p>Invoices can’t be edited after they’re sent.</p>
                    <p>
                    <div class="form-group d-flex flex-column align-items-left">

                        <label>
                            <b>Include on this email: <br /></b>
                        </label>
                        <input type="email" id="optional-email" class="form-control focus-first" placeholder="Add email (optional)">

                    </div>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Continue editing</button>
                    <button type="button" class="btn btn-primary btn-send">Send Invoice</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="withdrawModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Withdraw</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: unset !important;">
                    <div class="col-md-12 col-sm-12 col-xs-12 d-flex">
                        <p>How much ETH would you like to withdraw?</p>
                        <a href="javascript:void(0)" class="btn-sm btn-primary ml-2" style="color: whitesmoke;height: 28px;">max</a>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12 form-group d-flex flex-column">
                        <input type="text" class="form-control" placeholder="ETH">
                    </div>
                    <div style="display: flex;">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <input type="text" class="form-control" placeholder="Send to another address">
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <input type=" text" class="form-control" placeholder="Exchange for USD">
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group mt-4">
                        <h4>Withdraw Fee: 4.5% ETH</h4>
                        <p>LunarPay does not take a transaction fee when your customer paid the invoice. Instead we take a 4.5% withdraw fee.
                            This saves you hundreds in gas fees on the ethereum network. </p>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group d-flex flex-column">
                        <input type="text" class="form-control" placeholder="Wallet Address" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Withdraw</button>
                </div>
            </div>
        </div>
    </div>
</div>