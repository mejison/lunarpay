<?php $this->load->view("general/invoice_component_modal") ?>
<?php $this->load->view("general/person_component_modal") ?>
<?php $this->load->view("general/product_component_modal") ?>

<style>
    #invoices_datatable tr:hover {
        background-color: #f3f3f3ad;
        cursor: pointer;
    }
</style>

<div id="invoices-container">
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
                    <?php if (isset($view_data['title'])) : ?>
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h3 class="mb-0"><i class="fas fa-print"></i> <?= $view_data['title'] ?></h3>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-neutral float-right top-table-bottom btn-GENERAL-add-invoice" data-context="invoice">
                                        <i class="fas fa-print"></i> <?= langx('create_invoice') ?>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <h3 class="mb-0">Current Balance: 2.345 ETH $500</h3>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-neutral float-right top-table-bottom btn-GENERAL-add-withdraw" data-context="withdraw">
                                        <?= langx('withdraw') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row py-2" id="filters">
                        <!-- filter example
                        <div id="invoices_datatable_div_organization_filter" class="col-md-3 col-sm-12 ml-4" style="display: none;">
                            <label for="invoices_organization_filter"><?= langx('company:') ?></label>
                            <select id="invoices_datatable_organization_filter" class="custom-select custom-select-sm">
                                <option value="">All Companies</option>
                            </select>
                        </div>-->
                    </div>
                    <div class="table-responsive py-4">
                        <table id="invoices_datatable" class="table table-flush" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID [Hidden]</th>
                                    <th style="width: 75px">Invoice Reference</th>
                                    <th>Total</th>
                                    <th>Fee Covered</th>
                                    <th>Subtotal</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Due Date</th>
                                    <th>Created At</th>
                                    <th style="width: 75px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <?php echo form_open("", ['role' => 'form', 'id' => 'token_form']); ?>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>