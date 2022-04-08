
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
<div id="referals-container">
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
                                    <button class="btn btn-neutral float-right top-table-bottom btn-add-referal-component" data-context="referal_component_context"> 
                                        <i class="fas accusoft"></i>
                                        <?= langx('send_referrals_link') ?>
                                    </button>
                                </div>
                            </div>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="row py-2" id="filters">
                    
                    </div>

                    <div class="table-responsive py-4">
                        <table id="referals_datatable" class="table table-flush table-hover" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-left" style="width:200px;padding-left:60px;">Email</th>
                                    <th>Name</th>
                                    <th>Date Sent</th>
                                    <th>Date Registered</th>
                                    <th style="display:none"></th>
                                    <th style="display:none"></th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="newReferal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">New Referal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Share my referal code with...</p>
                        <p>
                        <div class="form-group d-flex flex-column align-items-left">
                            <label>
                                <b>Full Name:<br /></b>    
                            </label>       
                            <input  type="text" id="referal-name" class="form-control" placeholder="Add a name">
                            
                        </div>
                        <div class="form-group d-flex flex-column align-items-left">
                            <label>
                                    <b>Email<br /></b>    
                            </label>       
                            <input  type="text" id="referal-email"  class="form-control" placeholder="Add an email">
                        </div>
                        <div class="form-group d-flex flex-column">
                            <label>
                                    <b>Message<br /></b>    
                            </label>       
                            <textarea rows="5" id="referal-message"  class="form-control  align-items-left">Invite to friend with my code!!</textarea>
                        </div>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-send" id="referal-send">Send</button>
                    </div>
                </div>
            </div>
    </div>
</div>