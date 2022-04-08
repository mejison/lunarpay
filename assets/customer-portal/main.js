(function () {

    //add csrf token, it is pending
    $(document).ready(async function () {
        await portal.start(); //loader shown on header before loading html
        loader('hide');
    });

    let portal = {
        htmlCnt: '#portal-container', //html container
        view_config: view_config, //set on footer outside this scope but global
        base_api: APP_BASE_URL + 'customer/apiv1/',
        payment_link_data: null,
        apiKey:null, //will be loaded within the payment link resource
        formatter: new Intl.NumberFormat('en-US', {style: 'currency', currency: 'USD', minimumFractionDigits: 2}),
        login_action: null,
        sc_input: $(this.htmlCnt + ' #sc-1'), //Current security code input working
        email_regexp : /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/,
        user_logged: null,
        user_data: null,
        payment_option_selected: null,
        registering: false, // When modal is Registering instead Logging
        is_refreshed_token: false,
        options: {
            environment: null, //TEST / LIVE //will be loaded within the invoice resource
            style: {
                input: {
                    "font-size": "13px",
                    "color": "#1A1A1A!important",
                    "font-family":"Open Sans, sans-serif;",
                    "font-weight": "300"

                },
                "::placeholder": {
                    "color": "#bbbbc2!important",
                    "font-family":"Open Sans, sans-serif;",
                    "font-weight": "300"
                }
            },
            fields: {
                cardNumber: {
                    selector: "#cardNumber",
                    placeholder: "Card Number",
                },
                expiryDate: {
                    selector: "#cardExpiry",
                    placeholder: "MM / YY"
                },
                cvv: {
                    selector: "#cardCvc",
                    placeholder: "CVV"
                }
            }
        },
        payment_option: 'cc', //cc or bank,
        setEvents: function () { //@private method
            $(document).on('click', portal.htmlCnt + ' .payment-selector .option-container', function () {
                $(portal.htmlCnt + ' .payment-selector .option-container').removeClass('is-selected');
                $(this).hide().fadeIn('slow').addClass('is-selected');
                let type = $(this).attr('type');
                $('[data-option-container]').hide();
                $('[data-option-container="' + type + '"]').fadeIn()
            });
            $(portal.htmlCnt + ' .payment-selector .option-container').first().click(); //set first payment method as default

            $(document).on('click', portal.htmlCnt + ' #pay_bank',function () {
                send_data = {};
                var form = $('#bank_form');
                var data = form.serializeArray();
                $.each(data, function () {
                    send_data[this.name] = this.value;
                });
                send_data['email'] = $('#email').val();
                send_data = JSON.stringify(send_data);
                console.log(send_data);
            });

            //Check if email is registered
            $(document).on('input', portal.htmlCnt + ' #email',function (e) {
                let email_value = $(this).val();
                let email_validation =  String(email_value).toLowerCase()//email regular expression validation
                    .match(portal.email_regexp);
                if(email_validation){
                    let send_data = {};
                    send_data['username'] = email_value;
                    send_data['org_id'] = portal.payment_link_data.church_id;
                    if(parseInt(portal.payment_link_data.campus_id))
                        send_data['suborg_id'] = portal.payment_link_data.campus_id;
                    $.ajax({
                        url: `${portal.base_api}auth/account_exists`, type: "POST", data: JSON.stringify(send_data) , dataType: "json",
                        success: function (data) {
                            if(data.response.status){
                                $(portal.htmlCnt + ' #btn_sign_in_modal').show();
                                $(portal.htmlCnt + ' .save_card_container').hide();
                            } else {
                                $(portal.htmlCnt + ' #btn_sign_in_modal').hide();
                                $(portal.htmlCnt + ' .save_card_container').show();
                            }
                        },
                        error: function (jqXHR, textStatus, errorJson) {
                            if (typeof jqXHR.responseJSON !== 'undefined' &&
                                typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {
                                alert(jqXHR.responseJSON.message);
                            } else {
                                alert("error: " + jqXHR.responseText);
                            }
                        }
                    });
                }
            });

            //Sign-in/Sign-up Modal Open
            $(portal.htmlCnt + ' #sign-in-modal').on('show.bs.modal', async function () {
                loader('show');
                $(portal.htmlCnt+ ' .sign_in_email').text($(portal.htmlCnt + ' #email').val());
                $(portal.htmlCnt + " #security-code-table input").val('');

                if(portal.registering){ // Sign-up
                    $(portal.htmlCnt + ' .is_registering').text('Sign up');
                    $(portal.htmlCnt + " .sc-status").hide();
                    $(portal.htmlCnt + " .sc-status-info").show();
                    $(portal.htmlCnt + " #security-code-table input").attr('disabled',false);
                } else { // //Sign-in
                    $(portal.htmlCnt + ' .is_registering').text('Sign in');
                    $(portal.htmlCnt + " .sc-status").hide();
                    $(portal.htmlCnt + " .sc-status-info").show();
                    $(portal.htmlCnt + " #security-code-table input").attr('disabled',false);
                }

                portal.sc_input = $(portal.htmlCnt + ' #sc-1');
                await portal.generateSecurityCode();
                loader('hide');
            });

            $(portal.htmlCnt + ' #sign-in-modal').on('shown.bs.modal', function () {
                portal.sc_input.focus();
            });

            $(portal.htmlCnt + ' #sign-in-modal').on('hidden.bs.modal', function () {
                portal.registering = false;
                $(portal.htmlCnt + " .sc-status").hide();
                $(portal.htmlCnt + " .sc-status-info").show();
                $(portal.htmlCnt + " #security-code-table input").attr('disabled',false);
                portal.sc_input.focus();
            });

            //Security Code Input Functionality
            let security_code_table = $(portal.htmlCnt + " #security-code-table");
            security_code_table.on('keypress',function (e) {
                let charCode = String.fromCharCode(e.keyCode);
                let is_number = /^[0-9]+$/.test(charCode);
                if (is_number) {
                    let target = e.srcElement || e.target;
                    target = $(target);
                    let next = target.parent().next().find('input');
                    if (next !== null) {
                        next.focus();
                        portal.sc_input = next;
                    }
                } else {
                    e.preventDefault();
                }
            });
            security_code_table.on('keydown','input',function (e) {
                if(e.keyCode == 8) { // BACKSPACE
                    let target = e.srcElement || e.target;
                    target = $(target);
                    let previous = target.parent().prev().find('input');
                    if (previous !== null) {
                        previous.focus();
                        previous.val('');
                        portal.sc_input = previous;
                    }
                }
            });
            security_code_table.on('focus','input',function (e) {
                if($(this) !== portal.sc_input) {
                    setTimeout(function () { // FIX Maximum call stack size exceeded ERROR
                        portal.sc_input.focus();
                    },1);
                    e.preventDefault();
                }
            });
            security_code_table.on('keyup','input',async function (e) {
                e.stopPropagation();
                let security_code_inputs = $(portal.htmlCnt + " #security-code-table input");
                let security_code = '';
                $.each(security_code_inputs,function (key,value) {
                    security_code += $(value).val();
                });
                if(security_code.length === 5){
                    $(security_code_inputs).attr('disabled','disabled');
                    $(portal.htmlCnt + " .sc-status").hide();
                    $(portal.htmlCnt + " .sc-status-verifying").show();
                    if(portal.registering){
                        await portal.register(security_code)
                    } else {
                        await portal.login(security_code);
                    }
                    setTimeout(function () {
                        $(portal.htmlCnt +' #sign-in-modal').modal('hide');
                    },1000)
                }
            });

            //Register Data Functionality
            $(portal.htmlCnt + ' .save_data').change(function () {
                if ($(this).is(':checked')) {
                    portal.registering = true;
                    let email_value = $(portal.htmlCnt + ' #email').val().trim();
                    let email_holder_name = $(portal.htmlCnt + ' #holder_name').val().trim();
                    let is_valid = true;
                    let error_message = '';

                    if(!email_value){
                        error_message = 'The Email field is required';
                        is_valid = false;
                    } else if(!email_value.match(portal.email_regexp)){
                        error_message = 'Invalid Email';
                        is_valid = false;
                    } else if(!email_holder_name){
                        error_message = 'Holder Name is required';
                        is_valid = false;
                    }

                    if(is_valid){
                        $(portal.htmlCnt + " #sign-in-modal").modal('show');
                    } else {
                        notify({'title': 'Notification', 'message': error_message});
                        $(portal.htmlCnt + ' .save_data').prop('checked',false);
                    }
                }
            });

            //Change Qty
            $(portal.htmlCnt + ' #product_list').on('input','input',function () {
                portal.calculateTotal()
            });

            //Sign Out
            $(portal.htmlCnt + ' #sign_out').on('click',async function () {
                loader('show');
                await portal.sign_out();
                loader('hide');
            });

            $(portal.htmlCnt + ' #pay_bank').on('click',async function () {
                loader('show');
                await portal.bank_payment();
                loader('hide');
            });

            $(portal.htmlCnt + ' #pay_wallet').on('click',async function () {
                loader('show');
                await portal.wallet_payment();
                loader('hide');
            });

            $(portal.htmlCnt + ' #cancel_change_payment_option').on('click',async function () {
                $(portal.htmlCnt + ' .payment_selected_container').show();
                $(portal.htmlCnt + ' .payment_options').hide();
            });

            $(portal.htmlCnt + ' #change_payment_option').on('click',async function () {
                $(portal.htmlCnt + ' .payment_selected_container').hide();
                $(portal.htmlCnt + ' .payment_options').show();
            });

            $(portal.htmlCnt + ' .payment_options').on('click','.payment_option',function () {
                $(portal.htmlCnt + ' .payment_option').removeClass('theme_color text_theme_color');
                $(this).addClass('theme_color text_theme_color');
                portal.payment_option_selected = {'id' : $(this).attr('data-id')};
                let text_payment = $(this).html();
                setTimeout(function () {
                    $(portal.htmlCnt + ' .payment_options').hide();
                    $(portal.htmlCnt + ' #payment_selected').html(text_payment);
                    $(portal.htmlCnt + ' .payment_selected_container').show();
                },100);
            });

            $(portal.htmlCnt + ' .payment_options').on('click','.add_new_option',function () {
                $(portal.htmlCnt + ' .payment_options').hide();
                $(portal.htmlCnt + ' .table_new_payment_option').show();
                $(portal.htmlCnt + ' .option-container.is-selected').click();
                $(portal.htmlCnt + ' .cancel_new_payment_option_container').show();
            });

            $(portal.htmlCnt + ' #cancel_new_payment_option').on('click', function () {
                $(portal.htmlCnt + ' .payment_options').show();
                $(portal.htmlCnt + ' .new_payment_option').hide();
                $(portal.htmlCnt + ' .cancel_new_payment_option_container').hide();
            });
        },
        login: async function(security_code){
            let send_data = {};
            send_data['username'] = $(portal.htmlCnt + ' #email').val();
            send_data['security_code'] = security_code;
            send_data['org_id'] = portal.payment_link_data.church_id;
            if(parseInt(portal.payment_link_data.campus_id))
                send_data['suborg_id'] = portal.payment_link_data.campus_id;
            await $.ajax({
                url: `${portal.base_api}auth/login`, type: "POST", data: JSON.stringify(send_data) , dataType: "json",
                success: async function (data) {
                    if(data.response.status === true){

                        if(data.response[auth_obj_var]){
                            try {
                                localStorage.setItem(auth_access_tk_var, data.response[auth_obj_var][auth_access_tk_var]);
                                localStorage.setItem(auth_refresh_tk_var, data.response[auth_obj_var][auth_refresh_tk_var]);
                            } catch (e) {}
                        }

                        $(portal.htmlCnt + " .sc-status").hide();
                        $(portal.htmlCnt + " .sc-status-success").show();
                        portal.user_logged = send_data['username'];
                        await portal.get_user();
                        portal.showLoggedView();
                    } else {
                        $(portal.htmlCnt + " .sc-status").hide();
                        $(portal.htmlCnt + " .sc-error-message").text(data.response.message);
                        $(portal.htmlCnt + " .sc-status-error").show();
                    }
                },
                error: function (jqXHR, textStatus, errorJson) {
                    if (typeof jqXHR.responseJSON !== 'undefined' &&
                        typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {

                        $(portal.htmlCnt + " .sc-status").hide();
                        $(portal.htmlCnt + " .sc-error-message").text(jqXHR.responseJSON.message);
                        $(portal.htmlCnt + " .sc-status-error").show();
                    } else {
                        $(portal.htmlCnt + " .sc-status").hide();
                        $(portal.htmlCnt + " .sc-error-message").text(jqXHR.responseText);
                        $(portal.htmlCnt + " .sc-status-error").show();
                    }
                }
            });
        },
        register: async function(security_code){
            let send_data = {};
            send_data['username'] = $(portal.htmlCnt + ' #email').val();
            send_data['name'] = $(portal.htmlCnt + ' #holder_name').val();
            send_data['security_code'] = security_code;
            send_data['org_id'] = portal.payment_link_data.church_id;
            if(parseInt(portal.payment_link_data.campus_id))
                send_data['suborg_id'] = portal.payment_link_data.campus_id;
            await $.ajax({
                url: `${portal.base_api}auth/register`, type: "POST", data: JSON.stringify(send_data) , dataType: "json",
                success: async function (data) {
                    $(portal.htmlCnt + " .sc-status").hide();
                    if(data.response.status === true){

                        if(data.response[auth_obj_var]){
                            try {
                                localStorage.setItem(auth_access_tk_var, data.response[auth_obj_var][auth_access_tk_var]);
                                localStorage.setItem(auth_refresh_tk_var, data.response[auth_obj_var][auth_refresh_tk_var]);
                            } catch (e) {}
                        }

                        $(portal.htmlCnt + " .sc-status-success").show();
                        portal.user_logged = send_data['username'];
                        await portal.get_user();
                        portal.showLoggedView();
                    } else {
                        $(portal.htmlCnt + " .sc-error-message").text(data.response.message);
                        $(portal.htmlCnt + " .sc-status-error").show();
                        $(portal.htmlCnt + " .save_data").prop('checked',false);
                    }
                },
                error: function (jqXHR, textStatus, errorJson) {
                    if (typeof jqXHR.responseJSON !== 'undefined' &&
                        typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {

                        $(portal.htmlCnt + " .sc-status").hide();
                        $(portal.htmlCnt + " .sc-error-message").text(jqXHR.responseJSON.message);
                        $(portal.htmlCnt + " .sc-status-error").show();
                    } else {
                        $(portal.htmlCnt + " .sc-status").hide();
                        $(portal.htmlCnt + " .sc-error-message").text(jqXHR.responseText);
                        $(portal.htmlCnt + " .sc-status-error").show();
                    }
                }
            });
        },
        sign_out: async function(){
            let header = "Bearer ";
            try {
                if(localStorage && localStorage.getItem(auth_access_tk_var)){
                    header += localStorage.getItem(auth_access_tk_var);
                }
            } catch (e) {}

            await $.ajax({
                url: `${portal.base_api}auth/sign_out`, type: "POST", dataType: "json", headers: {'Authorization': header},
                success: function (data) {
                    if(data.response.status){
                        portal.user_logged = null;
                        portal.user_data = null;
                        portal.signoutView();
                    }
                },
                error: async function (jqXHR, textStatus, errorJson) {
                    try {
                        let json = jqXHR.responseJSON;
                        if(json.response && (json.response.errors === 'access_token_not_found' || json.response.errors === 'access_token_expired')){
                            await portal.refresh_token();
                            if(portal.is_refreshed_token) {
                                portal.is_refreshed_token = false;
                                await portal.sign_out();
                            }
                        }
                    } catch (e) {
                        console.log(e);
                    }
                }
            });
        },
        get_user: async function(){
            let header = "Bearer ";
            try {
                if(localStorage && localStorage.getItem(auth_access_tk_var)){
                    header += localStorage.getItem(auth_access_tk_var);
                }
            } catch (e) {}
            let send_data = {};
            send_data['org_id'] = portal.payment_link_data.church_id;
            if(parseInt(portal.payment_link_data.campus_id))
                send_data['suborg_id'] = portal.payment_link_data.campus_id;

            await $.ajax({
                url: `${portal.base_api}auth/get_user`, type: "POST", data: JSON.stringify(send_data) , dataType: "json", headers: {'Authorization': header},
                success: function (data) {
                    if(data.response.status){
                        portal.user_data = data.response.user;
                        portal.showLoggedView();
                    }
                },
                error: async function (jqXHR, textStatus, errorJson) {
                    try {
                        let json = jqXHR.responseJSON;
                        if(json.response && (json.response.errors === 'access_token_not_found' || json.response.errors === 'access_token_expired')){
                            await portal.refresh_token();
                            if(portal.is_refreshed_token) {
                                portal.is_refreshed_token = false;
                                await portal.get_user();
                            }
                        }
                    } catch (e) {
                        console.log(e);
                    }
                }
            });
        },
        showLoggedView: function(){
            $(portal.htmlCnt + ' #email_input').hide();
            $(portal.htmlCnt + ' .save_card_container').hide();
            $(portal.htmlCnt + ' #email').val(portal.user_logged);
            $(portal.htmlCnt + ' #email_logged').text(portal.user_logged);
            $(portal.htmlCnt + ' #email_logged_container').show();
            if(portal.user_data.sources){
                portal.payment_option_selected = portal.user_data.sources[0];
                if(portal.payment_option_selected){
                    $(portal.htmlCnt + ' .new_payment_option').hide();
                    $(portal.htmlCnt + ' .payment_selected_container').show();
                    if(portal.payment_option_selected.source_type === 'card') {
                        $(portal.htmlCnt + ' #payment_selected').html('<i class="fa fa-credit-card"></i> •••• •••• •••• '+portal.payment_option_selected.last_digits);
                    } else {
                        $(portal.htmlCnt + ' #payment_selected').html('<i class="fas fa-university"></i> •••• •••• •••• '+portal.payment_option_selected.last_digits);
                    }
                }
                let payment_options_container = $(portal.htmlCnt + ' .payment_options .list-group');
                payment_options_container.empty();
                $.each(portal.user_data.sources,function (key,value) {
                    let payment_text = '';
                    if(value.source_type === 'card')
                        payment_text = '<i class="fa fa-credit-card"></i> •••• •••• •••• '+value.last_digits;
                    else
                        payment_text = '<i class="fas fa-university"></i> •••• •••• •••• '+value.last_digits;
                    payment_options_container.append(`<li data-id="${value.id}" class="payment_option list-group-item ${key === 0 ? 'theme_color text_theme_color' : ''}">${payment_text}</li>`);
                });
                payment_options_container.append(`<li class="add_new_option list-group-item text-center"><a href="#">Add New Payment Option</a></li>`);

            }
            $(portal.htmlCnt + ' #sign_out').show()
        },
        signoutView: function(){
            $(portal.htmlCnt + ' #email_input').show();
            $(portal.htmlCnt + ' #email_logged_container').hide();
            $(portal.htmlCnt + ' .payment_selected_container').hide();
            $(portal.htmlCnt + ' .payment_options').hide();
            $(portal.htmlCnt + ' .cancel_new_payment_option_container').hide();
            $(portal.htmlCnt + ' .table_new_payment_option').show();
            $(portal.htmlCnt + ' .option-container.is-selected').click();
            $(portal.htmlCnt + ' #email_input').trigger('input');
            $(portal.htmlCnt + ' #sign_out').hide();
        },
        start: async function () { //@public
            portal.setEvents();
            if (portal.view_config.view == 'payment_link') { //load the view requested from the url
                await portal.getPaymentLinkFullData(view_config.payment_link.hash);
            }
            try {
                await portal.is_logged();
            } catch (e) {}
            portal.get_branding_data();
            portal.paysafe_init();
        },
        getPaymentLinkFullData: async function (hash) {
            await $.ajax({
                url: `${portal.base_api}payment_link/${hash}`, type: "GET", dataType: "json",
                success: function (data) {
                    portal.payment_link_data = data.response.payment_link;
                    portal.options.environment = data.response.payment_processor.env;
                    portal.apiKey = data.response.payment_processor.encoded_keys;
                    $(portal.htmlCnt + ' #company_name').text('Pay to '+portal.payment_link_data.organization.name);
                    $(portal.htmlCnt + ' #product_list').empty();
                    let total_amount = 0;
                    $.each(portal.payment_link_data.products,function (key,value) {
                        $(portal.htmlCnt + ' #product_list').append(`<div class="product_item row mb-2 pb-2 border-bottom" data-id="${value.id}" data-price="${value.product_price}" data-default-qty="${value.qty}"><div class="col-8 text-muted"><span class="h4">${value.product_name}</span></div><div class="h4 col-4">${portal.formatter.format(value.product_price)}</div><div class="col-12 product_qty mutted-text" >Qty ${value.is_editable === '1' ? `<input data-id="${value.id}" class="form-control form-control-sm d-inline-block ml-1" value="${value.qty}" min="1" max="${value.qty}" type="number" style="width: 60px">` : value.qty }</div></div>`);
                        total_amount += (value.product_price * value.qty);
                    });
                    $('.total_amount').text(portal.formatter.format(total_amount));
                    if(portal.payment_link_data.organization.region === 'CA'){
                        $(portal.htmlCnt + ' .bank_form').hide();
                        $(portal.htmlCnt + ' #eft_bank_form').show();
                    } else {
                        $(portal.htmlCnt + ' .bank_form').hide();
                        $(portal.htmlCnt + ' #ach_bank_form').show();
                    }
                },
                error: function (jqXHR, textStatus, errorJson) {
                    if (typeof jqXHR.responseJSON !== 'undefined' &&
                            typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {
                        alert(jqXHR.responseJSON.message);
                    } else {
                        alert("error: " + jqXHR.responseText);
                    }
                }
            });
        },
        calculateTotal : function () {
            let total_amount = 0;
            $.each($('.product_item'),function (key,value) {
                let price = $(value).attr('data-price');
                let qty = $(value).attr('data-default-qty');
                let qty_input = $(value).find('input');
                if(qty_input.length){
                    qty = qty_input.val();
                }
                total_amount += (price * qty)
            });
            $('.total_amount').text(portal.formatter.format(total_amount));
        },
        generateSecurityCode: async function () {
            let send_data = {};
            send_data['username'] = $(portal.htmlCnt + ' #email').val();
            send_data['org_id'] = portal.payment_link_data.church_id;
            if(parseInt(portal.payment_link_data.campus_id))
                send_data['suborg_id'] = portal.payment_link_data.campus_id;
            await $.ajax({
                url: `${portal.base_api}auth/generate_security_code`, type: "POST",data:JSON.stringify(send_data), dataType: "json",
                error: function (jqXHR, textStatus, errorJson) {
                    //loader('hide');
                    if (typeof jqXHR.responseJSON !== 'undefined' &&
                            typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {
                        alert(jqXHR.responseJSON.message);
                    } else {
                        alert("error: " + jqXHR.responseText);
                    }
                }
            });
        },
        get_branding_data: async function(){
            await $.get(portal.base_api+'organization/get_brand_settings/'+portal.payment_link_data.church_id+
                (portal.payment_link_data.campus_id ? '/'+portal.payment_link_data.campus_id : ''),function (result) {
                if(result.response.data) {
                    if (result.response.data.logo) {
                        $(portal.htmlCnt + '#logo').show();
                        $(portal.htmlCnt + '#logo').attr('src', result.response.data.entire_logo_url);
                    } else {
                        $(portal.htmlCnt + '#logo').hide();
                    }
                    let theme_color = result.response.data.theme_color ? result.response.data.theme_color : '#000000';
                    let text_theme_color = helpers.getTextColor(theme_color);
                    let style = `
                    .theme_color{
                        background: ${theme_color} !important;
                    }.theme_foreground_color{
                        color: ${theme_color} !important;
                    }
                    .text_theme_color{
                        color: ${text_theme_color} !important;
                    }
                    .email_background_color{
                        background: ${result.response.data.button_text_color ? result.response.data.button_text_color : '#F8F8F8'} !important;
                    }
                `;
                    $('#css_branding').html(style);
                }
            });
        },
        paysafe_init: async function (){
            paysafe.fields.setup(portal.apiKey, portal.options, function(instance, error) {
                $(portal.htmlCnt + ' #pay_cc').click(function () {
                    loader('show');
                    instance.tokenize(async function(instance, error, result) {
                        if (error) {
                            // display the tokenization error in dialog window
                            console.log(error);
                            $(portal.htmlCnt + ' #card_information .alert-validation').text(error.displayMessage);
                            loader('hide');
                        } else {
                            let send_data = {};
                            var form = $(portal.htmlCnt + ' #cc_form');
                            var data = form.serializeArray();
                            $.each(data, function () {
                                send_data[this.name] = this.value;
                            });
                            send_data['data_payment'] = {'single_use_token': result.token, 'postal_code' : $(portal.htmlCnt + ' #cardZip').val()};
                            send_data['username'] = $(portal.htmlCnt + ' #email').val();
                            send_data['data_payment']['first_name'] = $(portal.htmlCnt + ' #holder_name').val();
                            send_data['payment_method'] = 'credit_card';
                            send_data['products'] = [];
                            portal.payment_option = 'cc';
                            await portal.pay(send_data);
                            loader('hide');
                        }
                    });
                });
            });
        }, // CC Payment
        bank_payment: async function (){ //Bank Payment
            let send_data = {};
            send_data['data_payment'] = {};
            var form = $(portal.htmlCnt + ' #ach_bank_form');
            send_data['data_payment']['bank_type'] = 'ach';
            if(portal.payment_link_data.organization.region === 'CA'){
                form = $(portal.htmlCnt + ' #eft_bank_form');
                send_data['data_payment']['bank_type'] = 'eft';
            }
            var data = form.serializeArray();

            $.each(data, function () {
                send_data['data_payment'][this.name] = this.value;
            });
            send_data['username'] = $(portal.htmlCnt + ' #email').val();
            send_data['payment_method'] = 'bank_account';
            portal.payment_option = 'bank';

            await portal.pay(send_data);
            loader('hide');
        }, //Bank Payment
        wallet_payment: async function (){ //Wallet Payment
            let send_data = {};
            send_data['payment_method'] = portal.payment_option_selected.id;
            send_data['username'] = $(portal.htmlCnt + ' #email').val();
            portal.payment_option = 'wallet';
            send_data['data_payment'] = {};

            let header = "Bearer ";
            try {
                if(localStorage && localStorage.getItem(auth_access_tk_var)){
                    header += localStorage.getItem(auth_access_tk_var);
                }
            } catch (e) {}

            await portal.pay(send_data,header);
            loader('hide');
        },
        pay: async function(send_data,header = null){
            send_data['products'] = [];
            $.each($(portal.htmlCnt + ' #product_list .product_item'),function (key,value) {
                let link_product_id = $(value).attr('data-id');
                let qty = $(value).attr('data-default-qty');
                let qty_input = $(value).find('input');
                if(qty_input.length){
                    qty = qty_input.val();
                }
                send_data['products'].push({link_product_id: link_product_id, qty: qty});
            });

            await $.ajax({
                url: `${portal.base_api}pay/payment_link/`+portal.view_config.payment_link.hash, headers: {'Authorization': header}, type: "POST", data: JSON.stringify(send_data) , dataType: "json",
                success: function (data) {
                    if(data.response.status){
                        $(portal.htmlCnt + ' .payment-form').hide();
                        $(portal.htmlCnt + ' #payment_done').css("display", "flex").hide().fadeIn();
                        $(portal.htmlCnt + ' #product_list .product_item input').replaceWith(function () {
                            return `<span>${$(this).val()}</span>`;
                        });

                        $.each(data.response.payment_link.products,function (key,value) {
                            if(value.digital_content) {
                                $(portal.htmlCnt + ` #product_list .product_item[data-id="${value.id}"]`).append(`<a class="col-12 digital_content" href="${value.digital_content_url}">Download Deliverable</a>`);
                            }
                        });
                        $(portal.htmlCnt + ' #download_receipt').attr('href',data.response.payment_link.payments._receipt_file_url);
                    } else {
                        if(portal.payment_option === 'cc') {
                            $(portal.htmlCnt + ' #card_information .alert-validation').text(data.response.errors.join("\n"));
                        } else if(portal.payment_option === 'bank') {
                            $(portal.htmlCnt + ' #bank_information .alert-validation').text(data.response.errors.join("\n"));
                        } else {
                            $(portal.htmlCnt + ' .payment_selected_container .alert-validation').text(data.response.errors.join("\n"));
                        }
                    }
                },
                error: async function (jqXHR, textStatus, errorJson) {
                    if(header){
                        try {
                            let json = jqXHR.responseJSON;
                            if(json.response && (json.response.errors === 'access_token_not_found' || json.response.errors === 'access_token_expired')){
                                await portal.refresh_token();
                                if(portal.is_refreshed_token) {
                                    portal.is_refreshed_token = false;
                                    await portal.pay(send_data,header);
                                }
                            }
                        } catch (e) {
                            console.log(e);
                        }
                    } else {
                        if (typeof jqXHR.responseJSON !== 'undefined' &&
                            typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {
                            if (portal.payment_option === 'cc') {
                                $(portal.htmlCnt + ' #card_information .alert-validation').text(jqXHR.responseJSON.errors.join("\n"));
                            } else if (portal.payment_option === 'bank') {
                                $(portal.htmlCnt + ' #bank_information .alert-validation').text(jqXHR.responseJSON.errors.join("\n"));
                            } else {
                                $(portal.htmlCnt + ' .payment_selected_container .alert-validation').text(jqXHR.responseJSON.errors.join("\n"));
                            }
                        } else {
                            if (portal.payment_option === 'cc') {
                                $(portal.htmlCnt + ' #card_information .alert-validation').text(jqXHR.responseText.join("\n"));
                            } else if (portal.payment_option === 'bank') {
                                $(portal.htmlCnt + ' #bank_information .alert-validation').text(jqXHR.responseText.join("\n"));
                            } else {
                                $(portal.htmlCnt + ' .payment_selected_container .alert-validation').text(jqXHR.responseText.join("\n"));
                            }
                        }
                    }
                }
            });
        },
        is_logged: async function () {
            let header = "Bearer ";
            try {
                if(localStorage && localStorage.getItem(auth_access_tk_var)){
                    header += localStorage.getItem(auth_access_tk_var);
                }
            } catch (e) {}
            let send_data = {};
            send_data['org_id'] = portal.payment_link_data.church_id;
            if(parseInt(portal.payment_link_data.campus_id))
                send_data['suborg_id'] = portal.payment_link_data.campus_id;

            await $.ajax({
                url: `${portal.base_api}auth/is_logged`, type: "POST", data: JSON.stringify(send_data) , dataType: "json", headers: {'Authorization': header},
                success: async function (data) {
                    if(data.response.status){
                        portal.user_logged = data.response.data.email;
                        await portal.get_user();
                        portal.showLoggedView();
                    }
                },
                error: async function (jqXHR, textStatus, errorJson) {
                    try {
                        let json = jqXHR.responseJSON;
                        if(json.response && (json.response.errors === 'access_token_not_found' || json.response.errors === 'access_token_expired')){
                            await portal.refresh_token();
                            if(portal.is_refreshed_token) {
                                portal.is_refreshed_token = false;
                                await portal.is_logged();
                            }
                        }
                    } catch (e) {
                        console.log(e);
                    }
                }
            });

        },
        refresh_token: async function(){
            let header_refresh = "Bearer ";
            try {
                if(localStorage && localStorage.getItem(auth_refresh_tk_var)){
                    header_refresh += localStorage.getItem(auth_refresh_tk_var);
                }
            } catch (e) {}
            await $.ajax({
                type: "POST",
                url: `${portal.base_api}auth/refresh_token`,
                headers: {'Authorization': header_refresh},
                dataType: 'json',
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                }
            }).done(function (data, status) {
                if(data.response.status == true){
                    if(data.response[auth_obj_var]){
                        try {
                            localStorage.setItem(auth_access_tk_var, data.response[auth_obj_var][auth_access_tk_var]);
                            localStorage.setItem(auth_refresh_tk_var, data.response[auth_obj_var][auth_refresh_tk_var]);
                            portal.is_refreshed_token = true;
                        } catch (e) {}
                    }
                }
            })
        }
    };
}());