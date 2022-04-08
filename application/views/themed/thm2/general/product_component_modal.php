<div class="modal fade" id="product-component" >
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 740px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <?= langx('Product') ?>
                    <span style="font-size: 12.7px; padding-top: 20px; line-height: 24px; font-weight: normal; font-style: italic; display: none" class="subtitle">
                        <br>
                        <span class="organization_name" style="font-weight: bold"></span>
                    </span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                        </div>
                    </div>
                </div>
                <?php echo form_open("products/save", ['role' => 'form', 'id' => 'product_component_form' , 'autocomplete' => 'nope']); ?>
                <div class="row">
                    <div id="organization_field" class="col-md-6 d-none">
                        <div class="form-group">
                            <?php echo langx('company:', 'organization_id'); ?> <br />
                            <select class="form-control" name="organization_id" placeholder="">
                            </select>
                        </div>
                    </div>
                    <div id="suborganization_field" class="col-md-6 d-none">
                        <div class="form-group">
                            <?php echo langx('sub_organization:', 'suborganization_id'); ?> <br />
                            <select class="form-control" name="suborganization_id" placeholder="">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('name:', 'name'); ?> <br />
                            <input type="text" class="form-control focus-first" name="product_name" placeholder="Name" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('price:', 'price:'); ?> <br />
                            <input type="number" class="form-control" name="price" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo langx('recurrence:', 'recurrence'); ?> <br />
                            <select class="form-control" name="recurrence" placeholder="">
                                <option value="O" selected>One Time</option>
                                <option value="R">Periodically</option>
                            </select>
                        </div>
                    </div>
                    <div id="billing_period_container" class="col-md-6" style="display: none">
                        <div class="form-group">
                            <?php echo langx('Billing Period:', 'billing_period'); ?> <br />
                            <select class="form-control" name="billing_period" placeholder="">
                                <option value="daily" selected>Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="3_months">Every 3 months</option>
                                <option value="6_months">Every 6 months</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12" style="display: none;">
                        <div class="form-group">
                            <?php echo langx('description:', 'description'); ?> <br />
                            <textarea type="text" class="form-control" name="description" rows="4" placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr class="mt-1 mb-3"/>
                    </div>
                    <div class="col-md-12 d-none">
                        <div id="image_dropzone" class="dropzone dropzone-single"
                             data-toggle="dropzone"
                             data-dropzone-url="http://">
                            <div class="fallback">
                                <div class="custom-file">
                                    <input type="file" name="logo"
                                           class="custom-file-input"
                                           id="dropzoneBasicUpload"
                                           style="display: none;">
                                </div>
                            </div>

                            <div class="dz-preview dz-preview-single">
                                <div class="dz-preview-cover">
                                    <img class="dz-preview-img" src="" alt=""
                                         data-dz-thumbnail
                                         style="max-width: 200px;margin: 0 auto; display: flex;">
                                </div>
                            </div>

                            <div class="dz-message" style="padding: 3.7rem 1rem;"><span>Drop or Click here to Product Image</span>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <style>.tooltip-inner{max-width: 315px; width: 315px }</style>                        
                        <?php echo langx('deliverable', 'digital_content'); ?>
                        <label for="digital_content">PDF</label>
                        <?php echo langx('file', 'digital_content'); ?>&nbsp;&nbsp;
                        
                        <label style="text-align:center; position:relative; bottom: 2px" class="tooltip-help" data-toggle="tooltip" data-html="true" data-placement="right" 
                               title='You can upload a PDF file to be delivered to your customer once they have paid'>
                            <strong>?</strong>
                        </label>
                            
                        
                        <br />
                        <div class="custom-file">
                            <input type="file" accept=".pdf" id="digital_content" name="digital_content" class="custom-file-input d-none" lang="en">
                            <label id="digital_content_label" data-default-text="<?= langx('No file selected'); ?>"
                                   class="custom-file-label" for="digital_content"></label>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer justify-content-between">
                <button data-dismiss="modal" aria-label="Close" type="button" class="btn btn-default">Close</button>
                <button type="button" class="btn btn-primary btn-save" style="width: 200px">Create</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>