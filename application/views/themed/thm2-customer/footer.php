<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/jquery/dist/jquery.min.js"></script>
<script>
    var APP_BASE_URL = '<?= CUSTOMER_APP_BASE_URL ?>';
    var invoice = '<?= json_encode($view_data) ?>'

    function loader(option) {
        if (option === "show") {
            $("#cover_spin").show(0);
        } else if (option === "hide") {
            $("#cover_spin").hide(0);
        }
    }
    loader('show');
</script>
<!-- Argon JS -->

<script>
    var base_url = '<?= CUSTOMER_APP_BASE_URL ?>'; // base_url variable is used in cilte.js 
</script>
<script src="<?= BASE_ASSETS ?>js/cilte.js?v=3.0.0.1"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/js-cookie/js.cookie.js"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/js/argon.js?v=1.2.0"></script>
<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/moment.min.js"></script>
<script src="https://hosted.paysafe.com/js/v1/latest/paysafe.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.min.js"></script>
<script src="https://global.transak.com/sdk/v1.1/widget.js" async></script>
<script src="https://cdn.ethers.io/lib/ethers-5.2.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@walletconnect/web3-provider@1.7.1/dist/umd/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
<script type='text/javascript' src='<?= BASE_ASSETS ?>customer/main.js?v=3.0.0.7'></script>