<?php
$organizations = $view_data['organizations'];
?>
<input id="integration_tab" type="hidden" value="<?= $view_data ['tab']?>">
<div class="container-fluid">
    <div class="header-body">
        <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
                <h6 class="h2 text-white d-inline-block mb-0"></h6>
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                    <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                    </ol>
                </nav>
            </div>                
        </div>        
    </div>
</div>

<!-- Page content -->
<div class="container-fluid mt--6">
    <!-- Table -->
    <div class="row">
        <div class="col">
            <div class="card">
                <?php if (isset($view_data['title'])): ?>
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3 class="mb-0"><i class="fas fa-link"></i> <?= $view_data['title'] ?></h3>
                            </div>
                            <div class="col-sm-6">
                                <!--<button class="btn btn-neutral float-right top-table-bottom btn-add-statement" data-toggle="modal">
                                    <i class="fas fa-print"></i> <?= langx('button') ?>
                                </button>-->
                            </div>
                        </div>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <div class="ct-example">
                        <ul class="nav nav-tabs-code" id="nav-pills-tabs-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="nav-pills-tabs-zapier-tab" data-toggle="tab" href="#nav-pills-tabs-zapier" role="tab" aria-controls="nav-pills-tabs-zapier" aria-selected="false">
                                    Zapier 
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nav-pills-tabs-planning_center-tab" data-toggle="tab" href="#nav-pills-tabs-planning_center" role="tab" aria-controls="nav-pills-tabs-planning_center" aria-selected="true">
                                    Planning Center
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nav-pills-tabs-wordpress-tab" data-toggle="tab" href="#nav-pills-tabs-wordpress" role="tab" aria-controls="nav-pills-tabs-wordpress" aria-selected="true">
                                    Wordpress Plugin
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <hr style="margin: 0px 0px 20px 0px">
                            
                            <div id="nav-pills-tabs-zapier" class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-pills-tabs-zapier-tab">
                                <br>
                                <div class="">
                                    <table style="max-width: 600px; margin: auto">
                                        <tbody>
                                            <tr>
                                                <td style="width:56%; text-align: right"><img src="<?= BASE_ASSETS ?>thm2/images/brand/mainlogo.png?v=1.6" class="navbar-brand-img pull-right" alt="..." style="width: 62%;"></td>
                                                <td style="width:17%; text-align: center"><span style="font-size: 22px;"><i class="fas fa-link"></i></span></td>
                                                <td style="width:27%; text-align: left;"><img src="https://cdn.zapier.com/zapier/images/logomark250.png" class="navbar-brand-img pull-right" alt="..." style="width: 25%; margin:auto; margin-top: -2px; border-color: gray"></td>                                           
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="row justify-content-center">
                                        <div class="col-md-6" style="text-align: justify">
                                            <hr>
                                            Zapier is an online automation tool that connects your favorite apps,
                                            such as Gmail, Slack, Mailchimp, and more (+2000 apps).
                                            You can connect two or more apps to automate repetitive tasks.

                                            <br><br>
                                            An example of what you can do is to send each new donation to
                                            a Google spreadsheet.

                                            <br><br>
                                            <h4>ChatGive triggers</h4>

                                            <ul>
                                                <li>New Donation Received</li>
                                                <li>New Donor Registered</li>
                                                <li>New Recurrent Donation Created</li>
                                                <li>New Credit Card Expired</li>

                                            </ul>

                                        </div>
                                    </div>

                                    <br>

                                    <div class="text-center">

                                        To start building a new workflow connect ChatGive to your Zapier 
                                        account 
                                        <br>
                                        <br>
                                        <br>

                                        <a target="_blank" href="https://zapier.com/developer/public-invite/117206/b7cb50bdf3d2229f5a273e742d503dfe/" class="btn btn-primary" style="width: 320px">
                                            <i class="fas fa-link"></i> Connect
                                        </a>

                                        <br>
                                        <br>

                                    </div>
                                </div>
                            </div>
                            
                            <div id="nav-pills-tabs-planning_center" class="tab-pane tab-example-result fade" role="tabpanel" aria-labelledby="nav-pills-tabs-planning_center-tab">
                                <?php echo form_open("", ['role' => 'form', 'id' => 'form_controlcenter']); ?>
                                <div class="row">
                                    <div style="display: none;" class="col-md-12 text-center btn_planning_center_oauth_conn">
                                        <br>
                                        Connect ChatGive with your Planning Center account
                                        <br>
                                        <br>
                                        <a id="btn_planning_center_oauth_conn" href="" class="btn btn-primary" style="width: 320px">
                                            <i class="fas fa-link"></i> Connect
                                        </a>
                                        <br>
                                        <br>
                                    </div>
                                    <div style="display: none;" class="col-md-12 text-center btn_planning_center_push">
                                        <br>
                                        Your ChatGive Dashboard is now <strong>connected</strong> with your Planning Center account.
                                        <br><br>
                                        ChatGive will create a batch and will push all new donations, 
                                        <br>funds and people from all your organizations.
                                        <br>
                                        <br>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-7 text-right">
                                                <label for="commit_batch">                                                   
                                                    Commit Planning Center Batch once finished:
                                                </label><br><br>
                                            </div>
                                            <div class="col-sm-2 text-left">
                                                <label class="custom-toggle">                                    
                                                    <input type="checkbox" id="commit_batch" checked>
                                                    <span class="custom-toggle-slider rounded-circle"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <br>
                                        <a id="btn_planning_center_push"  href="" class="btn btn-primary" style="width: 200px">
                                            <i class="fas fa-upload"></i> Push Data
                                        </a>
                                        <a id="btn_planning_center_disconnect"  href="" class="btn btn-neutral" style="width: 200px">
                                            Disconnect
                                        </a>
                                        <br>
                                        <br>
                                    </div>
                                </div>
                                <?php echo form_close() ?>
                            </div>
                            
                            <div id="nav-pills-tabs-wordpress" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-pills-tabs-wordpress-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= langx('company:', 'organization_id', ["class" => "form-control-label"]); ?>
                                            <br/>
                                            <select class="form-control" name="organization_id" placeholder="">
                                                <?php foreach ($organizations as $organization) : ?>
                                                    <option value="<?= $organization['ch_id'] ?>" data-token="<?= $organization['token'] ?>">
                                                        <?= $organization['church_name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= langx('sub_organization:', 'suborganization_id', ["class" => "form-control-label"]); ?>
                                            <br/>
                                            <select class="form-control" name="suborganization_id" placeholder="">
                                                <option value="">Select a Sub Organization</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div>For easy installation on a wordpress website, download this plugin, install on your wordpress site and activate.</div><br>
                                    <a id="download_wordpress_plugin" href="#">Download Link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

