<?php //$this->load->view("general/product_component_modal") ?>
<div id="products-container">
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
                                    <h3 class="mb-0"><i class="fas accusoft"></i> <?= $view_data['title'] ?></h3>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-neutral float-right top-table-bottom btn-GENERAL-product-component" data-context="products"> 
                                        <i class="fas accusoft"></i><?= langx('create_product') ?>
                                    </button>
                                </div>
                            </div>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="row py-2" id="filters">
                    <!-- filter example
                        <div id="products_datatable_div_organization_filter" class="col-md-3 col-sm-12 ml-4" style="display: none">
                            <label for="statmnts_organization_filter"><?= langx('company:') ?></label>
                            <select id="products_datatable_organization_filter" class="custom-select custom-select-sm">
                                <option value="">All Companies</option>
                            </select>
                        </div>-->
                    </div>

                    <div class="table-responsive py-4">
                        <table id="products_datatable" class="table table-flush" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>Id[hidden]</th>
                                    <th>Reference</th>
                                    <th>Product name</th>
                                    <th>Price</th>
                                    <th>Created At</th>     
                                    <th>Action</th>                           
                                </tr>
                            </thead>
                        </table>
                        <?php
                            echo form_open('pages/remove', ["id" => "remove_product_form"]);
                            echo form_close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>