
<?php $this->load->view("general/product_component_modal") ?>

<script>
    var base_url = '<?= base_url() ?>';
    var system_letter = '<?= trim($this->SYSTEM_LETTER_ID)  ?>';
    var short_base_url = '<?= SHORT_BASE_URL ?>';
    var customer_base_url = '<?=CUSTOMER_APP_BASE_URL?>';
    var _current_payment_processor = '<?= $this->session->userdata('payment_processor_short') ?>';
    
    //it can contain elements such datatables, select2 for global use.
    //example of use: we have the donations datatable but we need to refresh it from a diferent script "add_transaction.js" we access it from this global
    var _global_objects = {donations_dt: null, funds_dt: null, donors_dt: null, myprofileview: null, triggerNew:null, currnt_org:null};

    //we save in a global object the current organizanization/suborganization
    _global_objects.currnt_org = <?= json_encode($this->session->userdata('currnt_org')) ?>;
    _global_objects.currnt_org_with_psf_tpl = null; //defined on org_selector.js
    
    var _global_payment_options = {
        'US' : {
            'CC': 'Card',
            'BANK': 'ACH Bank Transfer'
        }
    }

</script>

<?php if($this->session->flashdata('new')): //this is a flag for autolanching modals for creating records ex. new invoice, new products?>
<script>_global_objects.triggerNew = true;</script>
<?php endif;?>


<!-- Argon Scripts -->
<!-- Core (check this) verifyx -->
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/jquery/dist/jquery.min.js"></script>

<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/js-cookie/js.cookie.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>
<!-- Optional JS -->
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/chart.js/dist/Chart.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/chart.js/dist/Chart.extension.js"></script>

<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/nouislider/distribute/nouislider.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="<?= BASE_ASSETS ?>js/libs/imask.6.0.7.js"></script>

<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-notify/bootstrap-notify.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
 
<!-- Argon JS -->
<script src="<?= BASE_ASSETS_THEME ?>assets/js/argon.js?v=1.2.0"></script>
<!-- Demo JS - remove this in your project (check this verifyx) -->
<script src="<?= BASE_ASSETS_THEME ?>assets/js/demo.min.js"></script>

<script src="<?= BASE_ASSETS ?>js/cilte.js?v=3.0.0.2"></script>

<?php if ($view_index == 'acl/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/acl.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'organizations/index'): ?>    

    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script>
    global_data_helper.us_states = <?= json_encode($view_data['us_states']) ?>;
    global_data_helper.STARTER_STEP_BANK_CONFIRMATION = <?= User_model::STARTER_STEP_BANK_CONFIRMATION ?>;
    </script>

    <?php if ($this->session->userdata('payment_processor_short') === PROVIDER_PAYMENT_EPICPAY_SHORT): ?>
        <script src="<?= BASE_ASSETS ?>js/organization.js?v=3.0.0.1"></script>
    <?php elseif ($this->session->userdata('payment_processor_short') === PROVIDER_PAYMENT_PAYSAFE_SHORT): ?>
        <?php
        $CI = & get_instance();
        $CI->load->helper('paysafe');
        ?>
        <script>
        global_data_helper.paysafe_regions = <?= json_encode(getPaysafeRegions()) ?>;
        global_data_helper.twilio_available_countries_no_creation = <?= json_encode(TWILIO_AVAILABLE_COUNTRIES_NO_CREATION) ?>;
        </script>

        <script src="<?= BASE_ASSETS ?>js/organization_psf.js?v=3.0.0.1"></script>
    <?php endif; ?>

    <script src="<?= BASE_ASSETS ?>js/suborganization.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'suborganizations/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/suborganization.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'invoices/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datetimepicker.js"></script>
    <script src="<?= BASE_ASSETS ?>js/invoice.js?v=3.0.0.6"></script>

    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/invoice_component.js?v=3.0.0.9"></script>
    <script src="<?= BASE_ASSETS ?>js/products/product_component.js?v=3.0.0.4"></script>
<?php elseif ($view_index == 'donors/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/donor.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'donations/index'): ?>

    <!-- If we set a fund id table will load with that fund as filter -->
    <script>_global_objects.fund_id = <?= isset($fund_id) && $fund_id ? $fund_id : 'null' ?>;</script>

    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/donation.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'donations/recurring'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/recurring.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'donors/profile'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/donor_profile.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'dashboard/myprofile'): ?>
    <script src="<?= BASE_ASSETS ?>js/myprofile.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'funds/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/fund.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'statements/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datetimepicker.js"></script>
    <script src="<?= BASE_ASSETS ?>js/statement.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'products/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/products/products.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/products/product_component.js?v=3.0.0.4"></script>
<?php elseif ($view_index == 'batches/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/batches.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/batch_donations_form.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'payouts/index'): ?>
    <?php if ($this->session->userdata('payment_processor_short') == PROVIDER_PAYMENT_EPICPAY_SHORT): ?>
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datetimepicker.js"></script>
        <script src="<?= BASE_ASSETS ?>js/payout.js?v=3.0.0.1"></script>
    <?php elseif ($this->session->userdata('payment_processor_short') == PROVIDER_PAYMENT_PAYSAFE_SHORT): ?>
        <script src="<?= BASE_ASSETS ?>js/payout_psf.js?v=3.0.0.1"></script>
    <?php endif; ?>
<?php elseif ($view_index == 'install/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/install.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'invoices/view'): ?>
    <script src="<?= BASE_ASSETS ?>js/invoice-view.js?v=3.0.0.7"></script>
<?php elseif ($view_index == 'messaging/inbox'): ?>
    <script src="<?= BASE_ASSETS ?>js/messaging.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
<?php elseif ($view_index == 'communication/sms'): ?>
    <script src="<?= BASE_ASSETS ?>js/sms.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
<?php elseif ($view_index == 'customize_text/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/customize_text.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'pages/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/page.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'give_anywhere/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/give_anywhere.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'settings/integrations'): ?>
    <script src="<?= BASE_ASSETS ?>js/integrations.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'settings/team'): ?>
    <script src="<?= BASE_ASSETS ?>js/team.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'settings/branding'): ?>
    <script src="<?= BASE_ASSETS ?>js/branding.js?v=3.0.0.1"></script>
<?php elseif ($view_index == 'payment_links/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/payment_links.js?v=3.0.0.5"></script>
    <script src="<?= BASE_ASSETS ?>js/general/payment_link_component.js?v=3.0.0.1"></script>
    <script src="<?= BASE_ASSETS ?>js/products/product_component.js?v=3.0.0.5"></script>
<?php elseif ($view_index == 'payment_links/view'): ?>
    <script src="<?= BASE_ASSETS ?>js/general/payment_link-view.js?v=3.0.0.5"></script>
<?php elseif ($view_index == 'settings/referrals'): ?>
    <script src="<?= BASE_ASSETS ?>js/referals.js?v=1.0.0.5"></script>
<?php elseif ($view_index == 'getting_started/index'): ?>
    <?php    
    $CI = & get_instance();
    $CI->load->helper('paysafe'); //
    ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script>
    global_data_helper.us_states = <?= json_encode($view_data['us_states']) ?>;
    global_data_helper.paysafe_regions = <?= json_encode(getPaysafeRegions()) ?>;
    global_data_helper.twilio_available_countries_no_creation = <?= json_encode(TWILIO_AVAILABLE_COUNTRIES_NO_CREATION) ?>;
    </script>
    <script src="<?= BASE_ASSETS ?>js/getting_started.js?v=3.0.0.10"></script>
<?php endif; ?>

<?php if ($this->session->userdata('hide_intercom') !== TRUE && FORCE_HIDE_INTERCOM === FALSE): ?>
    <script>
    window.intercomSettings = {
        app_id: "sszism0f",
        name: "<?= $this->session->userdata("first_name") . ' ' . $this->session->userdata("last_name") ?>",
        email: "<?= $this->session->userdata("email") ?>",
        created_at: <?= $this->session->userdata("created_on") ?>,
        user_id: <?= $this->session->userdata("user_id") ?>
    };
    </script>

    <script>
        // We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/sszism0f'
        (function () {
            var w = window;
            var ic = w.Intercom;
            if (typeof ic === "function") {
                ic('reattach_activator');
                ic('update', w.intercomSettings);
            } else {
                var d = document;
                var i = function () {
                    i.c(arguments);
                };
                i.q = [];
                i.c = function (args) {
                    i.q.push(args);
                };
                w.Intercom = i;
                var l = function () {
                    var s = d.createElement('script');
                    s.type = 'text/javascript';
                    s.async = true;
                    s.src = 'https://widget.intercom.io/widget/sszism0f';
                    var x = d.getElementsByTagName('script')[0];
                    x.parentNode.insertBefore(s, x);
                };
                if (w.attachEvent) {
                    w.attachEvent('onload', l);
                } else {
                    w.addEventListener('load', l, false);
                }
            }
        })();
    </script>
<?php endif; ?>

 
<?php if($this->CI->SYSTEM_LETTER_ID=='H'){?>
    <script>
                $(document).ready(async function () {
                    $("#become-afiliate-button").on("click",function(){
                        $('#becomeAffiliate').modal("show");
                    })
                    $("#affiliate-send").on("click", function(){
                    if($("#affiliate-email").val()==='' || $("#affiliate-security").val()===''){
                            notify({title: 'Notification', 'message': 'All fields are mandatory.'});
                            return false;
                    }
                    $.post(base_url + 'settings/save_affiliate' , {zelle_social_security:$("#affiliate-security").val(), zelle_account_id:$("#affiliate-email").val(),csrf_token:$("input[name='csrf_token']").val()}, function (result) {
                        if(result.status){
                            notify({title: 'Notification', 'message': 'Affiliated correctly'});
                            $("#affiliated-nav").fadeOut(3000);
                            setTimeout(() => {
                                $('#becomeAffiliate').modal("hide");
                            }, 1000);
                        } 
                    })
                    })
                });
    </script>
<?php }?>
 
<script src="<?= BASE_ASSETS ?>js/org_selector.js?v=1.0.5"></script>
 
