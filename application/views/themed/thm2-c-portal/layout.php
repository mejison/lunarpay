<!doctype html>
<html lang="en">
    <head>
        <?php $this->load->view('header') ?>
        <?php $this->load->view('ui_loader'); ?>

        <script>
            function loader(option) {
                option === 'show' ? $('#cover_spin').show(0) : $('#cover_spin').hide(0);
            }
            loader('show');
        </script>
        <style id="css_branding"></style>
    </head>
    <body>
        <div id="portal-container" style="overflow: hidden">
            <div class="container-fluid-lg" style="min-height: 100vh;">
                <div class="text-center">
                    <!--<h5>Customer's Portal</h5>-->
                </div>
                <div class="row">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-4 mt-5">
                        <div class="brand justify-content-between">
                            <img id="logo" style="width: 50%" src="<?= BASE_ASSETS ?>thm2/images/brand/mainlogob.png?v=1.7" class="navbar-brand-img" alt="...">
                            <div style="clear: both"></div>
                            <span class="h3 font-weight-bold" id="company_name">[Company]</span>
                            <div class="h3 my-2 total_amount">$0</div>
                        </div>
                        <hr class="mb-0">
                        <div class="left-container text-left">
                            <?= $this->load->view('/html-components/product-detail', ['component_data' => []], true); ?> 
                        </div>
                        <div class="mb-5 d-none d-lg-block" style="position: absolute; bottom: 0;">Powered By <strong><?= COMPANY_NAME ?></strong></div>
                    </div>
                    <div class="col-lg-6">
                        <div style="min-height: 100vh" class="right-container pt-5">
                            <?= $this->load->view('/html-components/login-form', ['component_data' => []], true); ?> 
                            <?= $this->load->view('/html-components/payment-form', ['component_data' => []], true); ?>                       
                        </div>                    
                    </div>
                </div>
                <div class="row mt-3">

                </div>
            </div>        
            <?php $this->load->view('footer') ?>        
        </div>
    </body>
</html>