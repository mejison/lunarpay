(function () {

    $(document).ready(function () {
        donors_profile.setdonorsprofile_form();
        _global_objects.myprofileview = true;
    });
    var donors_profile = {
        setdonorsprofile_form: async function () {

            await $.getJSON( base_url+"assets/js/countrys/countrys.json?v=4", function( data ) {
                $.each(data,function (key,value) {
                    let selected = value.code === 'US' ? 'selected' : '';
                    $('#input-phone-code').append('<option data-phone="'+value.dial_code+'" '+selected+' value="'+value.code+'">'+value.code+' (+'+value.dial_code+')</option>');

                });
            });

            $('#input-phone-code').change(function () {
                $phone_code = $('#input-phone-code :selected').data('phone');
                $('#input-country-code-phone').val($phone_code);
                $country_code = $('#input-phone-code').val();
                $('#img_country').attr('src',base_url+'assets/images/countrys/'+$country_code.toLowerCase()+'.svg')
            });

            $phone_code = $('#input-phone-code').data('saved');
            if($phone_code !== ''){
                $('#input-phone-code').val($phone_code);
            }
            $('#input-phone-code').trigger('change');

            //Mask Profile Phone
            //Profile Phone
            if (document.querySelector('#input-phone')) {
                IMask(
                    document.querySelector('#input-phone'),
                    {
                        mask: '000000000000000',
                    });
            }

            //==== save profile
            $('#add_donor_profile_form .btn-save').on('click', function () {
                var btn = helpers.btn_disable(this);
                $('#add_donor_profile_form').find('.alert-validation').first().empty().hide();
                var data = $("#add_donor_profile_form").serializeArray();
                var save_data = {};
                $.each(data, function () {
                    save_data[this.name] = this.value;
                });
                save_data['id'] = $('#add_donor_profile_form').data('id');
                $.ajax({
                    url: base_url + 'donors/save_profile', type: "POST",
                    dataType: "json",
                    data: save_data,
                    success: function (data) {
                        if (data.status) {
                            success_message(data.message)
                        } else {
                            $('#add_donor_profile_form').find('.alert-validation').first().empty().html(data.message).fadeIn("slow");
                        }
                        typeof data.new_token.name !== 'undefined' ? $('input[name="' + data.new_token.name + '"]').val(data.new_token.value) : '';
                        helpers.btn_enable(btn);
                    },
                    error: function (jqXHR, textStatus, errorJson) {
                        helpers.btn_enable(btn);
                        if (typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {
                            alert(jqXHR.responseJSON.message);
                            location.reload();
                        } else {
                            alert("error: " + jqXHR.responseText);
                        }
                    }
                });
            });
        }

    };



}());

