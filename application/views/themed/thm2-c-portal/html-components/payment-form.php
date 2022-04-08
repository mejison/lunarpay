<div class="payment-form">
    <hr data-content="Payment Form" class="hr-text">
    <div class="payment-selector">
        <div id="email_input" class="form-group">
            <?php echo langx('email','email',['class'=>'text-muted']) ?>
            <input id="email" name="email" type="email" class="payment_input form-control">
            <!-- Button trigger modal -->
            <div class="float-right mr-1">
                <span id="btn_sign_in_modal" style="display: none;" type="button" data-toggle="modal" data-target="#sign-in-modal">
                    Sign-in
                </span>
            </div>
        </div>
        <div id="email_logged_container" class="card" style="display: none;">
            <div class="card-body p-3" style="background-color: #e9e9e9;">
                <p class="text-sm mb-0 font-weight-bold" style="opacity: .4">Email</p>
                <h6 id="email_logged" class="font-weight-bolder mb-0"></h6>
            </div>
        </div>
        <div class="cancel_new_payment_option_container mt-3 row" style="display: none;">
            <div class="col-9">
                <?php echo langx('enter_the_payment_details','enter_the_payment_details',['class'=>'text-muted']) ?>
            </div>
            <div class="col-3 text-right">
                <a id="cancel_new_payment_option" class="text-sm" href="#">Cancel</a>
            </div>
        </div>
        <table class="table_new_payment_option new_payment_option">
            <tbody>
                <tr>
                    <td>
                        <div class="option-container" type="cc">
                            <i class="fa fa-credit-card fa-2x"></i>
                            <div style="clear: both"></div>
                            Card
                        </div>
                    </td>
                    <td>
                        <div class="option-container" type="bank">
                            <i class="fas fa-university fa-2x"></i>
                            <div style="clear: both"></div>
                            Bank Transfer
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="card_information" data-option-container="cc" class="mt-3 new_payment_option form-group">
            <?php echo form_open('',['id'=>'cc_form']) ?>
            <?php echo langx('card_information','card_information',['class'=>'text-muted']) ?>
            <div class="form-row">
                <div id="cardNumber" class="col-12 form-control"></div>
                <div id="cardExpiry" class="col-6 form-control"></div>
                <div id="cardCvc" class="col-6 form-control"></div>
                <input id="cardZip" name="cc_zip" type="text" class="payment_input form-control" placeholder="Zip Code">
                <div id="holder_name_input" class="w-100 mt-2">
                    <?php echo langx('holder_name','holder_name',['class'=>'mt-2 text-muted pl-1']) ?>
                    <input id="holder_name" name="holder_name" type="text" class="payment_input form-control" >
                </div>
                <div class="save_card_container card mt-4" style="background-color: inherit">
                    <div class="card-body">
                        <div class="form-check" style="padding-left: 2rem">
                            <input class="form-check-input save_data" type="checkbox" value="">
                            <span class="form-check-label text-muted" for="save_data">
                                Save my data for a secure purchase process in one click
                            </span>
                            <div class="text-sm mt-2" style="color: #adadad;">Pay faster</div>
                        </div>
                    </div>
                </div>
                <button id="pay_cc" type="button" class="theme_color text_theme_color mt-3 btn btn-primary col-12">Pay</button>
                <div class="alert-validation"></div>
            </div>
            <?php echo form_close() ?>
        </div>
        <div id="bank_information" data-option-container="bank" class="mt-3 new_payment_option form-group">
            <?php echo form_open('',['id'=>'ach_bank_form','class'=>'bank_form']) ?>
            <?php echo langx('ACH_bank_account','bank_account',['class'=>'text-muted']) ?>
            <div class="form-row">
                <input name="first_name" type="text" class="bank_first_name payment_input form-control" placeholder="First Name">
                <input name="last_name" type="text" class="payment_input bank_medium_input form-control" placeholder="Last Name">
                <select name="account_type" class="payment_input bank_medium_input form-control">
                    <option value="">Select an account type</option>
                    <option value="SAVINGS">Savings</option>
                    <option value="CHECKING">Checking</option>
                    <option value="LOAN">Loan</option>
                </select>
                <input id="ach_bank_account_number" name="account_number" type="text" class="payment_input bank_medium_input form-control" placeholder="Account Number" maxlength="17">
                <input id="bank_routing" name="routing_number" type="text" class="payment_input bank_medium_input form-control" placeholder="Routing" maxlength="9">
                <input id="bank_city" name="bank_city" type="text" class="payment_input bank_medium_input form-control" placeholder="City">
                <input id="bank_street" name="bank_street" type="text" class="payment_input bank_medium_input form-control" placeholder="Street">
                <input name="postal_code" type="text" class="bank_zip payment_input form-control" placeholder="Zip Code">
            </div>
            <?php echo form_close() ?>
            <?php echo form_open('',['id'=>'eft_bank_form','class'=>'bank_form']) ?>
            <?php echo langx('EFT_bank_account','bank_account',['class'=>'text-muted']) ?>
            <div class="form-row">
                <input name="first_name" type="text" class="bank_first_name payment_input form-control" placeholder="First Name">
                <input name="last_name" type="text" class="payment_input bank_medium_input form-control" placeholder="Last Name">
                <input name="account_number" type="text" class="payment_input bank_medium_input form-control" placeholder="Account Number" maxlength="12">
                <input name="transit_number" type="text" class="payment_input bank_medium_input form-control" placeholder="Transit Number" maxlength="5">
                <input name="institution_id" type="text" class="payment_input bank_medium_input form-control" placeholder="Institution ID" maxlength="3">
                <input name="city" type="text" class="payment_input bank_medium_input form-control" placeholder="City">
                <input name="street" type="text" class="payment_input bank_medium_input form-control" placeholder="Street">
                <input name="postal_code" type="text" class="bank_zip payment_input form-control" placeholder="Zip Code">
            </div>
            <?php echo form_close() ?>
            <div class="form-row">
                <div class="save_card_container card mt-4" style="background-color: inherit">
                    <div class="card-body">
                        <div class="form-check" style="padding-left: 2rem">
                            <input class="form-check-input save_data" type="checkbox" value="">
                            <span class="form-check-label text-muted" for="save_data">
                                Save my data for a secure purchase process in one click
                            </span>
                            <div class="text-sm mt-2" style="color: #adadad;">Pay faster</div>
                        </div>
                    </div>
                </div>
                <button id="pay_bank" type="button" class="theme_color text_theme_color mt-3 btn btn-primary col-12">Pay</button>
                <div class="alert-validation"></div>
            </div>

        </div>
        <div class="payment_selected_container mt-3" style="display: none;">
            <div class="card">
                <div class="card-body p-3" style="background-color: #e9e9e9;">
                    <p class="text-sm mb-0 font-weight-bold" style="opacity: .4">Payment</p>
                    <div class="row">
                        <div id="payment_selected" class="col-md-9 text-muted"></div>
                        <div class="col-md-3"><a id="change_payment_option" class="text-sm" href="#">Change</a></div>
                    </div>
                </div>
            </div>
            <button id="pay_wallet" type="button" class="theme_color text_theme_color mt-3 btn btn-primary col-12">Pay</button>
            <div class="alert-validation"></div>
        </div>
        <div class="payment_options mt-4" style="display: none;">
            <div class="row">
                <div class="col-9">
                    <?php echo langx('select_a_payment_option','select_a_payment_option',['class'=>'text-muted']) ?>
                </div>
                <div class="col-3 text-right">
                    <a id="cancel_change_payment_option" class="text-sm " href="#">Cancel</a>
                </div>
            </div>
            <ul class="list-group mt-2">
            </ul>
        </div>
        <div class="text-center mt-3" id="sign_out" style="display: none;"><a href="#">Checkout as a Guest</a></div>
    </div>
</div>

<div id="payment_done" class="flex-column align-items-center" style="margin-top: 3em;  display: none;">
    <img src="<?= BASE_ASSETS ?>images/tick.png" style="width: 100px" alt="">
    <h5 class="text-muted mt-3">Thanks for the payment</h5>
    <a id="download_receipt" class="text-muted mt-2" style="text-decoration: underline; " href="#">Download Receipt <i class="fas fa-arrow-down"></i></a>
</div>

