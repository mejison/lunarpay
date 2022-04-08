
$(document).ready(function () {
    product_component.init();
});

var product_component = {
    htmlCont: '#product-component',
    product_id: null,
    org_id: null,
    org_name: null,
    suborg_id: null,
    suborg_name: null,
    is_select2: false,
    select2_id: null,
    image_changed: 0,
    digital_content_changed: 0,
    btnTrigger: '.btn-GENERAL-product-component', // ---- this is the button that lanunches de modal/component
    context: null, // ---- depending on the context the component will adopt specific behaviors, it is set on btn trigger
    init: function () {
        this.setBtnTriggerEvent();
        this.set_modal();
    },
    setBtnTriggerEvent: function () {
        let _self = this;
        $(document).on('click', _self.btnTrigger, async function () {
            loader('show');

            _self.image_changed = 0; //let the backend know when to save an image or not
            _self.digital_content_changed = 0; //let the backend know when to save an digital content or not

            _self.context = $(this).attr('data-context');
            if (typeof $(this).attr('data-is_select2') != 'undefined') {
                _self.context = 'invoices'; //change this verifyx
            }

            $(_self.htmlCont + ' .subtitle').hide();
            $(_self.htmlCont + ' #product_component_form')[0].reset();
            $(_self.htmlCont + ' #digital_content_label').text($(_self.htmlCont + ' #digital_content_label').attr('data-default-text'));
            $(_self.htmlCont + '').find('.alert-validation').first().empty().hide();
            $(_self.htmlCont + ' .btn-save').text('Create Product');

            _self.org_id = null;
            if (_self.context === 'products' || _self.context === 'invoices') {
                let $btn = $('button' + _self.btnTrigger); //read "<button>" do not read "<a>"
                if (typeof $btn.attr('data-org_id') === 'undefined' || $btn.attr('data-org_id').length === 0) {
                    notify({'title': 'Notification', 'message': 'Please choose an organization'});
                    loader('hide');
                    return false;
                }
                //filters provided:
                _self.org_id = parseInt($btn.attr('data-org_id'));
                _self.suborg_id = parseInt($btn.attr('data-suborg_id'));

                _self.org_name = $btn.attr('data-org_name');
                _self.suborg_name = $btn.attr('data-suborg_name');

                $(_self.htmlCont + ' .organization_name').html(_self.org_name
                        + (_self.suborg_id ? ' <span style="font-weight: normal;" > / </span> ' + _self.suborg_name : ''));

                $(_self.htmlCont + ' .subtitle').show(); //it wraps the organization name

            }

            if (_self.context === 'invoices') {
                _self.select2_id = $('button' + _self.btnTrigger).attr('data-is_select2_id');
                $(_self.select2_id).select2('close');
            }

            if (typeof $(this).attr('data-product_id') !== 'undefined') { // Update Setting load data
                $(_self.htmlCont + ' .btn-save').text('Update Product');
                await $.post(base_url + 'product/get', {id: _self.product_id}, async function (result) {
                    $(_self.htmlCont + ' input[name="name"]').val(result.name);
                    $(_self.htmlCont + ' input[name="description"]').val(result.description);
                    $(_self.htmlCont + ' input[name="price"]').val(result.price);
                }).fail(function (e) {
                    console.log(e);
                });
            }


            $(_self.htmlCont).modal('show');
            ////////////////////////
        });
    },
    set_modal: function () {
        let _self = this;
        $(_self.htmlCont + ' input').keypress(function (e) {
            if (e.which == 13) {
                _self.save();
                e.preventDefault();
                return false;
            }
        });

        $(_self.htmlCont).on('show.bs.modal', async function () {
            setup_multiple_modal(this);
        });

        $(_self.htmlCont).on('shown.bs.modal', async function () {
            $(_self.htmlCont).find(".focus-first").first().focus();
            loader('hide');
        });

        $(_self.htmlCont + ' select[name="recurrence"]').change(function () {
            if($(this).val() == 'O'){
                $(_self.htmlCont + ' #billing_period_container').hide();
            } else if($(this).val() == 'R'){
                $(_self.htmlCont + ' #billing_period_container').show();
            }
        });

        $(document).on('click', _self.htmlCont + ' .btn-save', function () {
            _self.save();
        });

        //Disable Auto Upload Dropzone
        var logo_dropzone = Dropzone.forElement('#image_dropzone');
        logo_dropzone.options.autoProcessQueue = false;
        logo_dropzone.options.autoDiscover = false;

        logo_dropzone.on('addedfile', function (file) {
            var reader = new FileReader();
            reader.onload = function () {
                var dataURL = reader.result;
                $('.image-temporal').remove();
                _self.logo_image = file;
                _self.logo_image_demo = dataURL;
                _self.image_changed = 1;
            };
            reader.readAsDataURL(file);
        });

        $(_self.htmlCont +' #digital_content').change(function (e) {
            if(e.target.files && e.target.files[0]){
                _self.digital_content_changed = 1;
                $('#digital_content_label').text(e.target.files[0].name);
            }
        })
    },
    save: function () {
        let _self = this;
        loader('show');

        let save_data = new FormData($(_self.htmlCont + " #product_component_form")[0]);
        save_data.append('id', _self.product_id);
        if (_self.image_changed === 1) {
            save_data.append('image_changed', _self.image_changed);
            save_data.append('image', _self.logo_image);
            _self.image_changed = 0;
        }
        if(_self.digital_content_changed){
            save_data.append('digital_content_changed', _self.digital_content_changed);
            _self.digital_content_changed = 0;
        }
        if (_self.org_id)
            save_data.append('organization_id', _self.org_id);

        if (_self.suborg_id)
            save_data.append('suborganization_id', _self.suborg_id);

        $.ajax({
            url: base_url + 'products/save', type: "POST",
            processData: false,
            contentType: false,
            data: save_data,
            success: function (result) {
                if (result.status) {
                    // ------ PRE COMMON LINES
                    $(_self.htmlCont).modal('hide');
                    if (_self.context === 'invoices') {
                        notify({title: 'Notification', 'message': result.message , 'align': 'center'});
                    } else {
                        notify({title: 'Notification', 'message': result.message});
                    }

                    // --------------------------

                    if (_self.context === 'products') {
                        $($('button' + _self.btnTrigger).attr('data-table_id')).DataTable().draw(false);
                    } else if (_self.context === 'invoices') {
                        $(_self.select2_id).select2("trigger", "select", {data: {'id': result.data.id, text: result.data.name,name: result.data.product_name, price: result.data.price}});
                    }

                } else if (result.status == false) {
                    $(_self.htmlCont).find('.alert-validation').first().empty().append(result.errors).fadeIn("slow");
                    $(_self.htmlCont).animate({scrollTop: 0}, 'fast'); //guide the user to see the error by scrolling to the top
                }
                loader('hide');

                typeof result.new_token.name !== 'undefined' ? $('input[name="' + result.new_token.name + '"]').val(result.new_token.value) : '';

            },
            error: function (jqXHR, textStatus, errorJson) {
                if (typeof e.responseJSON.csrf_token_error !== 'undefined' && e.responseJSON.csrf_token_error) {
                    alert(e.responseJSON.message);
                    window.location.reload();
                }
                loader('hide');
            }
        });
    },
    //Show Image on dropzone
    setImage: function (url) {
        var logo_element = $('#image_dropzone');
        var preview = logo_element.find('.dz-preview');
        if (url !== null) {
            var content_preview = `<div class="dz-preview-cover dz-image-preview image-temporal">
                        <img class="dz-preview-img" src="" data-dz-thumbnail="" 
                        style="max-width: 200px;margin: 0 auto; display: flex;">
                        </div>`;
            preview.append(content_preview);
            logo_element.addClass('dz-max-files-reached');
            logo_element.find('img').prop('src', url);
        } else {
            preview.empty();
            logo_element.removeClass('dz-max-files-reached');
        }
    }
};