<!DOCTYPE html>
<html>
    <head>  
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="<?= COMPANY_NAME ?> uses a smooth chat interface to guide your donors through a seamless giving experience.">
        <meta name="author" content="<?= COMPANY_NAME ?>">
        <title><?= COMPANY_NAME ?></title>
        <?php $this->load->view('header', ['view_index' => $view_index]) ?>
       
    </head>

    <body>       

        <?php $this->load->view('ui_loader') ?>

        <?php if (trim($this->SYSTEM_LETTER_ID) == 'L'){?>

            <?php $this->load->view('leftmenu', ['view_index' => $view_index]);?>

        <?php } elseif (trim($this->SYSTEM_LETTER_ID) == 'H'){?>
            
            <?php $this->load->view('leftmenu_coachpay', ['view_index' => $view_index]);?>

        <?php }?>
       
        <!-- Main content -->
        <div class="main-content" id="panel">
            <!-- Topnav -->
            <?php if (trim($this->SYSTEM_LETTER_ID) == 'H'){?>
                <nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom  just-dev" id="affiliated-nav">
                    <div style="backgroud-color:#525f7f "  class="container-fluid text-center px-4 alert alert-info mx-4 my-0">
                        <table>
                            <tr>
                                <td>
                                <p>Become a CoachPay affiliate and earn monthly payouts on your referrals processing. You can earn .2% on your referrals processed amounts under $1,000,000 in total referral processing and .4% 
                                on aything above $1,000,000 in total processing.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                     <button id="become-afiliate-button" class="btn btn-secondary">Become Affiliate</button>
                                 </td>
                            </tr>
                        </table>
                    </div>
                </nav>  
            <?php }?>
            <nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        
                        <?php $this->load->view('org_selector') ?>
                        
                        <ul class="navbar-nav align-items-center  ml-md-auto ">
                            <li class="nav-item d-xl-none">
                                <!-- Sidenav toggler -->
                                <div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin" data-target="#sidenav-main">
                                    <div class="sidenav-toggler-inner">
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                    </div>
                                </div>
                            </li>                                                                                    
                        </ul>
                        <ul class="navbar-nav align-items-center  ml-auto ml-md-0 ">
                            <li class="nav-item dropdown">
                                <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <div class="media align-items-center">
                                        <div class="media-body  ml-2  d-none d-block">
                                            <span class="mb-0 text-sm  font-weight-bold"><?= $this->session->userdata('user_name') ?> <i class="ni ni-bold-down"></i></span>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu  dropdown-menu-right">
                                    <div class="dropdown-header noti-title">
                                        <h6 class="text-overflow m-0"><?= $this->session->userdata('email') ?></h6>
                                    </div>
                                    <a href="<?= BASE_URL ?>dashboard/myprofile" class="dropdown-item">
                                        <i class="ni ni-single-02"></i>
                                        <span>My profile</span>
                                    </a>
                                    <?php if (trim($this->SYSTEM_LETTER_ID) == 'H') { ?>
                                        <a class="dropdown-item  just-dev"  href="http://help.chatgive.com/en/" target="_BLANK">
                                            <i class="ni ni-support-16"></i>
                                            <span>Help & Support</span>
                                        </a>
                                    <?php };?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?= BASE_URL ?>auth/logout" class="dropdown-item">
                                        <i class="ni ni-help"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <?php
            echo form_open('/', ["id" => "general_token_form"]);
            echo form_close();
            ?>
            <?= $content ?>

            <div class="container-fluid mt--6" style="margin-top: 5px!important">
                <!-- Footer -->
                <footer class="footer pt-0">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6">
                            <div class="copyright text-center  text-lg-left  text-muted">
                                <?= FOOTER_TEXT ?>
                            </div>
                        </div>
                        <div class="col-lg-6">

                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <?php $this->load->view('footer', ['view_index' => $view_index]) ?>   

        <div class="modal fade" tabindex="-1" role="dialog" id="becomeAffiliate">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Become Affiliate</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                        <div class="form-group d-flex flex-column align-items-left">
                            <label>
                                <b>Email:<br /></b>    
                            </label>       
                            <input  type="text" id="affiliate-email" class="form-control focus-first" placeholder="Add email">
                            
                        </div>
                        <div class="form-group d-flex flex-column align-items-left">
                            <label>
                                    <b>Social security<br /></b>    
                            </label>       
                            <input  type="password" id="affiliate-security"  class="form-control focus-first" placeholder="Add Social security">
                        </div>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-send" id="affiliate-send">Send</button>
                    </div>
                </div>
            </div>
        </div>     
    </body>
</html>
