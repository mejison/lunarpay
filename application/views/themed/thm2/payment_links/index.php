<?php $this->load->view("general/link_payment_component_modal") ?>
<?php $this->load->view("general/product_component_modal") ?>
<style>
    .dataTables_empty{text-align:center!important}
    #payment_links_datatable tr:hover {
        background-color: #f3f3f3ad;
        cursor: pointer;
    }
    .nav-link {
        color: #525f7f!important;
    }
</style>
<div id="links-container">
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
                                    <button class="btn btn-neutral float-right top-table-bottom btn-add-payment-link-component" data-context="payment_link_context"> 
                                        <i class="fas accusoft"></i><?= langx('create_payment_link') ?>
                                    </button>
                                </div>
                            </div>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="row py-2" id="filters">
                    
                    </div>

                    <div class="table-responsive py-4">
                        <table id="payment_links_datatable" class="table table-flush table-hover" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>id[hidden]</th>
                                    <th class="text-left" style="width:200px;padding-left:60px;">Link url</th>
                                    <th>Status</th>
                                    <th>Products</th>
                                    <th>Created</th>
                                    <th>&nbsp;</th>                           
                                </tr>
                            </thead>
                        </table>
                        <?php echo form_open("", ['role' => 'form', 'id' => 'token_form']); ?>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>