<!--
=========================================================
* Argon Dashboard PRO - v1.2.0
=========================================================
* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro
* Copyright  Creative Tim (http://www.creative-tim.com)
* Coded by www.creative-tim.com
=========================================================
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="<?= COMPANY_NAME ?> uses a smooth chat interface to guide your donors through a seamless giving experience.">
        <meta name="author" content="<?= COMPANY_NAME ?>">
        <title><?= COMPANY_NAME ?></title>
        <!-- Favicon -->
        <link rel="icon" href="<?= BASE_ASSETS ?>images/brand/qiconmain.png?v=1.1" type="image/png">
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
        <!-- Icons -->
        <link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/nucleo/css/nucleo.css" type="text/css">
        <link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" type="text/css">
        <!-- Argon CSS -->
        <link rel="stylesheet" href="<?= BASE_ASSETS_THEME ?>assets/css/argon.css?v=1.2.0.7" type="text/css">

        <style>
            .recaptcha-wrapper a{
                color: #6fa3ff;
            }
            .grecaptcha-badge { visibility: hidden; }
            .alert p{
                margin-bottom: 0px;
            }
        </style>
    </head>

    <body class="bg-default">
        <div class="main-content">
            <!-- Header -->
            <!--            <div class="text-center" style="padding:42px">
                            <img src="<?= BASE_ASSETS ?>images/brand/withtext.png" class="navbar-brand-img" alt="..." style="width: 300px; margin:auto">
                        </div>-->
            <div class="header bg-gradient-primaryx py-7" style="padding-top: 73px!important">
                <div class="container">
                    <div class="header-body text-center mb-7" style="margin-bottom: 10px!important">
                        <div class="row justify-content-center">
                            <div class="col-xl-6">                                
                                <?php if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'freechurchappbuilder.com') !== false) : ?>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td style="width:45%"><img src="<?= BASE_ASSETS ?>images/brand/appbuilderlogo.png" class="navbar-brand-img pull-right" alt="..." style="width: 100%; margin:auto; margin-top: -9px"></td>
                                                <td style="width:10%"><span style="font-size: 32px; color:black"><strong>+</strong></span></td>
                                                <td style="width:45%"><img src="<?= BASE_ASSETS ?>thm2/images/brand/mainlogo.png" class="navbar-brand-img pull-right" alt="..." style="width: 100%;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php else : ?>
                                    <table style="width:100%">
                                        <tbody>
                                            <tr>
                                                <td><img src="<?= BASE_ASSETS ?>thm2/images/brand/mainlogo.png" class="navbar-brand-img pull-right" alt="..." style="width: 300px; margin:auto"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php endif; ?>                                
                            </div>
                        </div>
                        <div class="row justify-content-center" style="padding: 25px 20px 35px 20px;">
                            <!--<h1 class="text-white">Welcome!</h1>-->
                            <div class="col-lg-6 col-md-8">
                                <p class="text-lead text-white">
                                    Welcome! ChatGive is 100% free to register and doesn't require
                                    a contract or credit card, so you can cancel anytime!
                                    <br>                                
                                </p>
                                <p class="text-lead text-white">
                                    Start by filling out some basic information below.                                    
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="separator separator-bottom separator-skew zindex-100">
                    <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg">
                    <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
                    </svg>
                </div>
            </div>
            <!-- Page content -->
            <div class="container mt--8 pb-5">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="card bg-secondary border-0 mb-0">
                            <div class="card-body px-lg-5 py-lg-5" style="padding-top: 50px!important; padding-bottom: 35px!important">
                                <?php echo form_open('auth/register', ['role' => 'role', 'id' => "registration_form"]); ?>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                                        </div>
                                    </div>                                    
                                </div>

                                <div class="form-group mb-3">
                                    <div class="input-group input-group-merge input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                        </div>
                                        <input class="form-control" placeholder="Email" type="email" name="email" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="input-group input-group-merge input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                        </div>
                                        <input class="form-control" placeholder="Password" type="password" name="password" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="input-group input-group-merge input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"></span>
                                        </div>
                                        <input class="form-control" placeholder="Full Name" type="text" name="full_name" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="input-group input-group-merge input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"></span>
                                        </div>
                                        <input class="form-control" placeholder="Phone" type="text" name="phone" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="input-group input-group-merge input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"></span>
                                        </div>
                                        <input class="form-control" placeholder="Organization Name" type="text" name="company" required>
                                    </div>
                                </div>

                                <div class="alert alert-default alert-dismissible fade show" role="alert" id="account_created_message" style="display: none; margin-top: 32px">
                                    <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                                    <span class="alert-text message"></span>                                            
                                </div>

                                <div class="text-center">
                                    <button id="btn_registration" style="min-width: 200px" type="button" class="btn btn-primary my-4">Create account</button>
                                </div>
                                <?php echo form_close(); ?>
                                <div class="recaptcha-wrapper">
                                    <div class="text-center" style="color: lightgray; font-size: 12px;">
                                        This site is protected by reCAPTCHA and the Google<br>
                                        <a href="https://policies.google.com/privacy">Privacy Policy</a> and
                                        <a href="https://policies.google.com/terms">Terms of Service</a> apply.                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <a href="<?= base_url() ?>auth/login" class="text-light"><small>Sign in</small></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer class="py-5" id="footer-main">
            <div class="container">
                <div class="row align-items-center justify-content-xl-between">
                    <div class="col-xl-6">
                        <div class="copyright text-center text-xl-left text-muted">
                            <?= FOOTER_TEXT ?>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Argon Scripts -->
        <!-- Core -->
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/jquery/dist/jquery.min.js"></script>
        <script src="<?= BASE_ASSETS_THEME ?>assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
        <!--<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/js-cookie/js.cookie.js"></script>-->
        <!--<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>-->
        <!--<script src="<?= BASE_ASSETS_THEME ?>assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>-->
        <!-- Argon JS -->
        <!--<script src="<?= BASE_ASSETS_THEME ?>assets/js/argon.js?v=1.2.0"></script>-->
        <!-- Demo JS - remove this in your project -->
        <!--<script src="<?= BASE_ASSETS_THEME ?>assets/js/demo.min.js"></script>-->
        <script>
            $(document).ready(function () {
                $('input[name="email"]').focus();
            });
            var base_url = '<?= base_url() ?>';
        </script>
        <script src="<?= BASE_ASSETS ?>js/cilte.js?v=<?= date('YmdHis') ?>"></script>

        <script src="https://www.google.com/recaptcha/api.js?render=<?= RECAPTCHA_PUBLIC_KEY ?>"></script>
        <script>
            var recaptcha_public_key = '<?= RECAPTCHA_PUBLIC_KEY ?>';
            var recaptcha_FE_enabled = <?= RECAPTCHA_ENABLED ? 'true' : 'false' ?>;
        </script>

        <script src="<?= BASE_ASSETS ?>js/registration.js?v=<?= date('YmdHis') ?>"></script>
        <script>
            window.intercomSettings = {
                app_id: "sszism0f"
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
        <!--PROOF PIXEL-->
        <script src="https://cdn.useproof.com/proof.js?acc=KUxOeOdYQ0ZQqV9WeFREN6aw4Qv2" async></script>
        <!--END PROOF PIXEL--> 
    </body>

</html>