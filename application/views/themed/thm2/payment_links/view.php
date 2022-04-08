<style>
    .card-stats .card-body {
        min-height: 120px;
    }
    .d-block{
        font-size: .9rem !important; /*improvex*/
        font-weight: 500; /*improvex*/
    }
    
    /*custom style for invoice status*/
  .invoiceStatus span.badge {font-size: .84em; width: auto!important; padding: 6px 14px; margin-left: -4px; font-weight: 600}
  
</style>
 
<!-- Header-->
<!-- Header -->
<div class="header pb-6 d-flex align-items-center" style="min-height: 146px; background-size: cover; background-position: center top;">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8" style="background-color: inherit!important; background: inherit!important"></span>
    <!-- Header container -->
    <div class="container-fluid align-items-center">
        <div class="row">
            <div class="col-lg-7 col-md-10">
                <h1 class="display-2"></h1>
                <p class="mt-0 mb-5" style="margin:0!important; margin-top: -10px!important"></p>
            </div>
        </div>
    </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="row" >
        <div class="col-xl-12 order-xl-1 align-items-center">
            <div class="row justify-content-center">
                <!--<div class="col-lg-2"></div>-->
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <h3 class="mb-0">Payment Link</h3>
                                </div>
                                    <div class="col-8 text-right">
                                        <button   class="btn btn-primary btn-edit-product float-right top-table-bottom m-2" data-hash="<?=$links->hash?>"  type="button">
                                            <span class="btn-inner--text">Save</span>
                                        </button>     
                                    </div>
                            </div>
                        </div>
                        <div class="card-body px-5">
                           
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-default alert-dismissible alert-validation" style="display: none">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">                                        
                                        <div class="col-lg-12 text-left invoiceStatus">
                                            <h6 class="heading-small mb-1 pb-2"><?=$links->_status?></h6>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">#ID</span>
                                                <span class="d-block h3"> <?=$links->id?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Company</span>
                                                <span class="d-block h3"><?=$links->organization->name?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Sub Organization</span>
                                                <span class="d-block h3"><?= isset($links->suborganization) ? $links->suborganization->name : '-' ?></span>
                                             </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Created</span>
                                                <span class="d-block h3"><?= ($links->created_at) ?   date("F j, Y",strtotime($links->created_at)) : ' - ' ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Payment options</span>
                                                <span class="d-block h3">
                                                    <?= (in_array('CC',json_decode($links->payment_methods,true)) && in_array('BANK',json_decode($links->payment_methods,true))) ? 'Credit Card - Bank' : (in_array('CC',json_decode($links->payment_methods,true))? 'Credit Card':  'Bank')?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <span class="h6 surtitle text-light text-muted">Link</span>
                                                <span class="d-block h3">
                                                    <?= $links->_link_url?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                            <?php echo form_open("", ['role' => 'form', 'id' => 'token_form']); ?>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>        
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h3 class="mb-0">Products</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body px-5" >
                                <div class="row mb-3">
                                    <div class="col h6 surtitle text-light text-muted">Product name</div>
                                    <div class="col h6 surtitle text-light text-muted text-center">Let customers adjust quantity</div>
                                    <div class="col-2 h6 surtitle text-light text-muted text-center">Qty</div>
                                    <div class="col-2 h6 surtitle text-light text-muted text-center">Price</div>
                                    <div class="col-2 h6 surtitle text-light text-muted text-right">Total</div>
                                </div>
                                <form id="edit_product_form">
                                <?php  $sum=0; foreach ($links->products as $product) { $sum= $sum+($product->qty*$product->product_price); ?>
                                    <div class="row">
                                        <div class="col">
                                                <div class="form-group">
                                                    <span class="d-block h4 mb-0"><?= $product->product_name?> </span>
                                                    <?= $product->digital_content ? '<a class="text-xs" href="'.$product->digital_content_url.'">Download Deliverable</a>' : '' ?>
                                                </div>
                                        </div>
                                        <input type="hidden" class="product_id" value="<?=$product->product_id?>">
                                        <input type="hidden" class="payment_link_product_id_<?=$product->product_id?>" value="<?=$product->id?>">
                                        <div class="col text-center pt-2 ">
                                        <label class="custom-toggle  custom-toggle">
                                            <input type="checkbox" <?= $product->is_editable == 1 ? 'checked' : '' ?>  class="form-control editable_<?=$product->product_id?>" name="editable">
                                            <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
                                        </label>
                                        </div>   
                                        <div class="col-2 text-center">
                                            <input type="number" id="<?=$product->product_id?>" min="1" max="1000" class="form-control qty_<?=$product->product_id?>" value="<?=$product->qty?>" style="width:80px!important; margin: auto" />
                                        </div>
                                        <div class="col-2 text-center">
                                                <div class="form-group">
                                                    <span class="d-block h4 mb-0">$<?= number_format($product->product_price, 2, '.', ',')?></span>
                                                </div>
                                        </div>
                                        <div class="col-2 text-right">
                                                <div class="form-group">
                                                    <span class="d-block h3">$<?= number_format($product->product_price*$product->qty, 2, '.', ',')?></span>
                                                </div>
                                        </div>
                                    </div>
                                <?php };?>
                                </form>
                                    <div class="row">
                                    <div class="col"><span class="h6 surtitle text-light text-muted">Total</span></div>
                                    <div class="col"><span class="h4  text-light">&nbsp;</span></div>
                                    <div class="col"><span class="h4  text-light">&nbsp;</span></div>
                                    <div class="col text-right"><span class="d-block h3"> $<?=number_format($sum, 2, '.', ',')?></span></div>
                                    </div>
                                </div>
                            </div>
                </div>
            </div> 
        </div>        
    </div>
</div>
 