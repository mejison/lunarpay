<?php $this->load->view("appcues/script") ?>
<?php $this->load->view("appcues/identity_client") ?>
<?php
$this->CI = & get_instance();
$this->CI->load->model('setting_model');

$this->CI->SYSTEM_LETTER_ID = $this->CI->setting_model->getItem('SYSTEM_LETTER_ID');


?>

<link rel="icon" href="<?= BASE_ASSETS ?>images/brand/qiconmain.png?v=1.3" type="image/png">

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">

<link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/nucleo/css/nucleo.css" type="text/css">
<link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" type="text/css">

<link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/datatables.net-select-bs4/css/select.bootstrap4.min.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style> /* Fix Conflict when select2 and bootstrap-datepicker is working together */
    span.select2-container.select2-container--default.select2-container--open {
        z-index: 9999;
    }
</style>

<link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/css/argon.css?v=1.2.1.3" type="text/css">

<!-- font -->
<link rel="stylesheet" href="<?= BASE_ASSETS ?>css/fonts/baloobhaina2/style.css">

<style type="text/css">
    
    .btn-inside-cell {
        font-weight: bold!important
    }
    table.dataTable th td, table.dataTable tr td {
        padding-left: 15px!important;
        padding-right: 7px!important;
        padding-bottom: 5px!important;
        padding-top: 16px;
    }
    .bold-weight {
        font-weight: bold!important
    }

    /*select2 multiple selection tag css - adjustments*/
    li.select2-selection__choice {
        background-color: #525f7f !important;
        line-height: 1.5 !important;
        padding: .625rem .625rem .5rem !important;
        color: #fff !important;
        border-radius: .25rem !important;
        background: #525f7f;
        box-shadow: 0 1px 2px rgb(68 68 68 / 25%);
        font-size: 66% !important;
        margin: .125rem !important;
        margin-right: 5px !important;
    }

    span.select2-selection.select2-selection--multiple {
        padding: 0 !important;
    }

    ul.select2-selection__rendered {
        padding: .25rem !important;
        margin: 0 !important;
    }

    /*select 2 FIX BUG WHEN USING MULTPLE - THE SEARCH INPUT IS HIDDEN AND SHRINKED TO 0 WIDTH*/
    .select2-container .select2-search--inline {
        display: block!important
    }
    li.select2-search .select2-search__field {
        margin-left: 5px!important;
        margin-top: 8px!important;
        font-size: 14px!important;
    }
    /*-----*/

    /*hint for helping the user to know what the input is for*/
    .hint-under-input {
        margin-top:7px; 
        font-size:13px; 
        font-style: italic;
    }

    /*boostrap tags input adjustments */
    .bootstrap-tagsinput {
        width: 100%;
        border: 1px solid hsl(210, 14%, 83%);
        border-radius: .25rem;
        min-height: 46px
    }

    .bootstrap-tagsinput input {
        margin-top: 8px;
    }

    .bootstrap-tagsinput input:not([size]) {
        width: 20px;
    }

    .badge-default {
        background-color: #525f7f !important; /*theme color*/
        font-weight:normal!important
    }

    .badge-secondary {
        border: solid 1px lightgray;
    }
        
    .select2-container .select2-selection--single{
        font-size: inherit !important;
    }
    
    /*from-group required class will add an * the left of the label*/
    .form-group.required label:after {
        content:" *";
        color:#525f7f;
        font-weight: bold;
    }

    /* FIX IMAGE COVER ON DROPZONE*/
    img.dz-preview-img {
        max-width: none !important;
        object-fit: contain;
    }

    .dz-preview-cover.dz-image-preview {
        display: flex;
        align-items: center;
    }

</style>
 
<link rel="stylesheet" href="<?= BASE_ASSETS ?>css/cilte.css?v=1.0.0.1">

<link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/sweetalert2/dist/sweetalert2.min.css">

<style>
    .swal2-popup .swal2-styled.swal2-confirm {
        background-color: #525f7f!important;        
    }
    .swal2-popup .swal2-styled.swal2-cancel {
        background-color: #acb4c7!important;
    }
</style>
<?php if ($view_index == 'install/index'): ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/widget/chat-widget-demo.css?v=<?= date('YmdHis') ?>">
<?php endif; ?>

<script>
    var global_data_helper = {};
    var is_dev = true;
</script>

<?php if (HIDE_FUTURE_FEATURES): ?>
    <?php
    //is_dev (javascript variable) and HIDE_FUTURE_FEATURES (PHP Variable) have 
    ///the exact same and unique purpose: Future features must not show up on production environments 
    //but on development environments only
    ?>
    <style>
        .just-dev{
            display: none !important;
        }
    </style>
    <script>
        is_dev = false;
    </script>
<?php endif; ?>