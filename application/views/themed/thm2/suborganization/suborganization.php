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
                                <h3 class="mb-0"><i class="fas fa-home"></i> <?= $view_data['title'] ?></h3>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-neutral float-right top-table-bottom btn-add-suborganization" data-toggle="modal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="table-responsive py-4">
                    <div id="suborganizations_datatable_div_organization_filter" class="col-md-3 col-sm-12 d-inline-block p-1" style="display: none;">
                        <label for="suborganizations_datatable_organization_filter"><?= langx('company:') ?></label>
                        <select id="suborganizations_datatable_organization_filter" class="custom-select custom-select-sm">
                            <option value="">All Companies</option>
                        </select>
                    </div>

                    <table id="suborganizations_datatable" class="table table-flush" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th><?= langx("sub_organization_name") ?></th>
                                <th><?= langx("address") ?></th>
                                <th><?= langx("phone") ?></th>
                                <th><?= langx("pastor") ?></th>
                                <th><?= langx("description") ?></th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add_suborganization_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="overlay d-flex justify-content-center align-items-center">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>
            <div class="modal-header">
                <h4 class="modal-title"><?= langx('save_sub_organization') ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open("suborganization/save_suborganization", ['role' => 'form', 'id' => 'add_suborganization_form']); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('sub_organization_name:', 'suborganization_name'); ?> <br />
                            <input type="text" class="form-control focus-first" name="suborganization_name" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('company:', 'organization_id'); ?> <br />
                            <select class="form-control" name="organization_id" placeholder="">
                                <option value="">Select a Company</option>
                                <?php foreach ($view_data['organizations'] as $organization) : ?>
                                    <option value="<?= $organization['ch_id'] ?>"><?= htmlspecialchars($organization['church_name'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('address:', 'address'); ?> <br />
                            <input type="text" class="form-control" name="address" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('phone:', 'phone'); ?> <br />
                            <input type="text" class="form-control" name="phone" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('pastor:', 'pastor'); ?> <br />
                            <input type="text" class="form-control" name="pastor" placeholder="">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php echo langx('description:', 'description'); ?> <br />
                            <textarea type="text" class="form-control" name="description" rows="4" placeholder=""> </textarea>
                        </div>
                    </div>

                </div>
                <?php echo form_close(); ?>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-save">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>