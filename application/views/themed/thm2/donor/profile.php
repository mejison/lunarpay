<style>
    .card-stats .card-body {
        min-height: 120px;
    }
</style>

<?php $this->load->view("general/add_transaction_modal") ?>
<?php $this->load->view("general/person_component_modal") ?>

<?php $profile = $view_data['profile']; ?>
<!-- Header -->
<!-- Header -->
<div class="header pb-6 d-flex align-items-center" style="min-height: 146px; background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8" style="background-color: inherit!important; background: inherit!important"></span>
    <!-- Header container -->
    <div class="container-fluid align-items-center">
        <div class="row">
            <div class="col-lg-7 col-md-10">
                <h1 class="display-2 text-white"><?= $profile->first_name ?> <?= $profile->last_name ?></h1>
                <p class="text-white mt-0 mb-5" style="margin:0!important; margin-top: -10px!important">
                    Registration: <?= date('m/d/Y', strtotime($profile->created_at)) ?>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="row" style="margin-top: 20px">
                <!--<div class="col-lg-2"></div>-->
                <div class="col-lg-3">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Given (NET)</h5>
                                    <span class="h2 font-weight-bold mb-0">$<?= number_format($profile->net, 2, '.', '') ?></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                                        <i class="ni ni-money-coins"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm">
                                <?= $profile->first_date_formatted ? '<span class="text-success mr-2"><i class="fa fa-arrow-up"></i></span>' : '' ?>
                                <span class="text-nowrap"><?= $profile->first_date_formatted ? 'Since ' . $profile->first_date_formatted : '' ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Month</h5>
                                    <span class="h2 font-weight-bold mb-0">$<?= number_format($profile->net_month, 2, '.','') ?></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                                        <i class="ni ni-money-coins"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm">
                                <span class="text-success mr-2"><i class="fa fa-arrow-up"></i></span>
                                <span class="text-nowrap">Last 30 Days</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 order-xl-1">
            <div class="row">
                <!--<div class="col-lg-2"></div>-->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><b>Customer Profile</b></h3>
                                </div>
                                <div class="col-4 text-right">

                                    <button class="btn btn-neutral btn-GENERAL-person-component float-right top-table-bottom" data-person_id="<?= $profile->id ?>" type="button">
                                        <span class="btn-inner--icon"><i class="fas fa-pen"></i></span>
                                        <span class="btn-inner--text">Edit Customer</span>
                                    </button>
                                    <button class="btn btn-neutral btn-GENERAL-add-transaction float-right top-table-bottom mr-2" data-org_id="<?= $profile->org_id ?>" data-suborg_id="<?= $profile->suborg_id ?>" data-context="donor-profile" data-donor_id="<?= $profile->id ?>" data-donor_name="<?= $profile->name ?>" type="button">
                                        <span class="btn-inner--icon"><i class="fas fa-dollar-sign"></i></span>
                                        <span class="btn-inner--text">Create Transaction</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="padding-left: 60px">
                            <?php echo form_open("donors/save_profile", ['role' => 'form', 'id' => 'add_donor_profile_form', 'data-id' => $profile->id]); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <h6 class="heading-small text-muted mb-4">Account information</h6>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light">First Name</span>
                                                <span class="d-block h3 text-white"><?= $profile->first_name ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light">Last Name</span>
                                                <span class="d-block h3 text-white"><?= $profile->last_name ?></span>
                                             </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light">Email Address</span>
                                                <span class="d-block h3 text-white"><?= $profile->email ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light">Phone</span>
                                                <span class="d-block h3 text-white"><?= $profile->phone_code ? '+'.$profile->phone_code . ' ' .$profile->phone : $profile->phone ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light">Address</span>
                                                <span class="d-block h3 text-white"><?= $profile->address ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                                <div clasS="col-md-3" style="padding-left: 0px; padding-right: 0px">
                                    <h6 class="heading-small text-muted mb-4 text-center">PAYMENT METHODS</h6>
                                    <?php foreach ($profile->saved_sources as $source) : ?>
                                        <div class="card">
                                            <!-- Card header -->
                                            <div class="card-header">
                                                <div class="row align-items-center">
                                                    <div class="col-8">
                                                        <h3 class="h4 mb-0" style="color: gray">
                                                            <?=
                                                            $source['source_type'] == 'bank' ? ''
                                                                    . '<i style="font-size: 1.5em" class="fas fa-university"></i>&nbsp;&nbsp;&nbsp;<b>Bank</b>' :
                                                                    ''
                                                                    . '<i style="font-size: 1.5em" class="fas fa-credit-card"></i>&nbsp;&nbsp;&nbsp;<b>Card</b>'
                                                            ?>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body text-center" style="padding:10px">
                                                &nbsp;&nbsp;&nbsp;<span style="font-size:2.2em">···· ···· ····</span> <span style="font-size: 1.2em"><strong><?= $source['last_digits'] ?></strong></span>
                                                <?php if ($source['source_type'] == 'card') : ?>
                                                    | <span style="font-style: italic; font-size: 0.9em">Expires: <?= $source['exp_month'] ?>/<?= $source['exp_year'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>                                        
                                    <?php endforeach; ?>
                                    <?php if(!$profile->saved_sources): ?>
                                        <p class="text-sm text-center">No data found</p>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <?php echo form_close(); ?>

                        </div>
                    </div>
                </div>        
            </div>
        </div>        
    </div>
</div>