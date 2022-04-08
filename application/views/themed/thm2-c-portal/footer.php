<!-- jQuery and Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>

<script>    
    var APP_BASE_URL = '<?= CUSTOMER_APP_BASE_URL ?>';
    var view_config = <?= $view_data ?>;
    var auth_obj_var = '<?= WIDGET_AUTH_OBJ_VAR_NAME ?>';
    var auth_access_tk_var = '<?= WIDGET_AUTH_ACCESS_TOKEN_VAR_NAME ?>';
    var auth_refresh_tk_var = '<?= WIDGET_AUTH_REFRESH_TOKEN_VAR_NAME ?>';
</script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap-notify/bootstrap-notify.min.js"></script>
<script> var base_url = '<?= CUSTOMER_APP_BASE_URL ?>'; // base_url variable is used in cilte.js </script>
<script src="<?= BASE_ASSETS ?>js/cilte.js?v=3.0.0.2"></script>
<script src="https://hosted.paysafe.com/js/v1/latest/paysafe.min.js"></script>
<script src="<?= BASE_ASSETS ?>customer-portal/main.js?v=1.0.5"></script>