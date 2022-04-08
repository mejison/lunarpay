<script>
    var base_url = '<?= base_url() ?>';
    var short_base_url = '<?= SHORT_BASE_URL ?>';    
    var _current_payment_processor = '<?= $this->session->userdata('payment_processor_short') ?>';
    
    //it can contain elements such datatables, select2 for global use.
    //example of use: we have the donations datatable but we need to refresh it from a diferent script "add_transaction.js" we access it from this global
    var _global_objects = {donations_dt : null, funds_dt : null, donors_dt : null, myprofileview: null};
</script>
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
<style>
    .alert-notify:not(.alert-info):not(.alert-success):not(.alert-warning):not(.alert-danger) {
        background-color: #010c4c;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<!-- Argon JS -->
<script src="<?= BASE_ASSETS_THEME ?>assets/js/argon.js?v=1.2.0"></script>
<!-- Demo JS - remove this in your project (check this verifyx) -->
<script src="<?= BASE_ASSETS_THEME ?>assets/js/demo.min.js"></script>

<script src="<?= BASE_ASSETS ?>js/cilte.js?v<?= date('YmdHis') ?>"></script>

<?php if ($view_index == 'acl/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/acl.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'organizations/index'): ?>    
    
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script>
        global_data_helper.us_states = <?= json_encode ($view_data['us_states'])?>;        
    </script>
    
    <?php if($this->session->userdata('payment_processor_short') === PROVIDER_PAYMENT_EPICPAY_SHORT): ?>
        <script src="<?= BASE_ASSETS ?>js/organization.js?v=<?= date('YmdHis') ?>"></script>
    <?php elseif($this->session->userdata('payment_processor_short') === PROVIDER_PAYMENT_PAYSAFE_SHORT): ?>
        <?php 
            $CI                  = & get_instance();
            $CI->load->helper('paysafe');
        ?>
        <script>
            global_data_helper.paysafe_regions = <?= json_encode (getPaysafeRegions()) ?>;
            global_data_helper.twilio_available_countries_no_creation = <?= json_encode(TWILIO_AVAILABLE_COUNTRIES_NO_CREATION) ?>;
        </script>
        
        <script src="<?= BASE_ASSETS ?>js/organization_psf.js?v=<?= date('YmdHis') ?>"></script>
    <?php endif; ?>
    
    <script src="<?= BASE_ASSETS ?>js/suborganization.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'suborganizations/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/suborganization.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'donors/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/donor.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'donations/index'): ?>
    
    <!-- If we set a fund id table will load with that fund as filter -->
    <script>_global_objects.fund_id = <?= isset($fund_id) && $fund_id ? $fund_id : 'null' ?>;</script>
    
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/donation.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'donations/recurring'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/recurring.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'donors/profile'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/donor_profile.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'dashboard/myprofile'): ?>
    <script src="<?= BASE_ASSETS ?>js/myprofile.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'funds/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/fund.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/add_transaction.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'statements/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datetimepicker.js"></script>
    <script src="<?= BASE_ASSETS ?>js/statement.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'batches/index'): ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script src="<?= BASE_ASSETS ?>js/batches.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/batch_donations_form.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS ?>js/general/person_component.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'payouts/index'): ?>
    <?php if ($this->session->userdata('payment_processor_short') == PROVIDER_PAYMENT_EPICPAY_SHORT): ?>
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datetimepicker.js"></script>
        <script src="<?= BASE_ASSETS ?>js/payout.js?v=<?= date('YmdHis') ?>"></script>
    <?php elseif ($this->session->userdata('payment_processor_short') == PROVIDER_PAYMENT_PAYSAFE_SHORT): ?>
        <script src="<?= BASE_ASSETS ?>js/payout_psf.js?v=<?= date('YmdHis') ?>"></script>
    <?php endif; ?>
<?php elseif ($view_index == 'install/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/install.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'messaging/inbox'): ?>
    <script src="<?= BASE_ASSETS ?>js/messaging.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
<?php elseif ($view_index == 'communication/sms'): ?>
    <script src="<?= BASE_ASSETS ?>js/sms.js?v=<?= date('YmdHis') ?>"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
<?php elseif ($view_index == 'customize_text/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/customize_text.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'pages/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/page.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'give_anywhere/index'): ?>
    <script src="<?= BASE_ASSETS ?>js/give_anywhere.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'settings/integrations'): ?>
    <script src="<?= BASE_ASSETS ?>js/integrations.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'settings/team'): ?>
    <script src="<?= BASE_ASSETS ?>js/team.js?v=<?= date('YmdHis') ?>"></script>
<?php elseif ($view_index == 'getting_started/index'): ?>
    <?php 
        $CI                  = & get_instance();
        $CI->load->helper('paysafe');
    ?>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
    <script>
        global_data_helper.us_states = <?= json_encode ($view_data['us_states'])?>;
        global_data_helper.paysafe_regions = <?= json_encode (getPaysafeRegions()) ?>;
        global_data_helper.twilio_available_countries_no_creation = <?= json_encode(TWILIO_AVAILABLE_COUNTRIES_NO_CREATION) ?>;
    </script>
    <script src="<?= BASE_ASSETS ?>js/getting_started.js?v=<?= date('YmdHis') ?>"></script>
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