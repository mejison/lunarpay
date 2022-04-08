(function () {

    $(document).ready(function () {
        integrations.init();
        integrations.setButtonsEvents();
        if($('#integration_tab').val() == 'pcenter') {
            $('#nav-pills-tabs-planning_center-tab').click();
        }
    });
    let integrations = {
        init: function () {
            loader('show');
            $.ajax({
                url: base_url + 'integrations/planningcenter/validatetoken', type: "GET",
                dataType: "json",
                cache: false,
                success: function (data) {
                    loader('hide');
                    $('#btn_planning_center_oauth_conn').attr('href', data.oauth_url);
                    if (data.conn_status) {
                        $('.btn_planning_center_push').show();                        
                    } else {
                        $('.btn_planning_center_oauth_conn').show();
                    }
                },
                error: function (jqXHR, textStatus, errorJson) {
                    loader('hide');
                    if (typeof jqXHR.responseJSON.status !== 'undefined' && jqXHR.responseJSON.status == false) {
                        alert(jqXHR.responseJSON.message);
                        location.reload();
                    } else {
                        alert("error: " + jqXHR.responseText);
                    }
                }
            });
        },
        setButtonsEvents: function () {
            $('#btn_planning_center_push').on('click', function (e) {
                let btn = helpers.btn_disable(this);
                $.ajax({
                    url: base_url + 'integrations/planningcenter/startpush', type: "POST",
                    dataType: "json",
                    data: {'commit': $('#commit_batch').is(':checked') ? 1 : 0},
                    success: function (result) {
                        helpers.btn_enable(btn);
                        console.log(result);
                        if (result.status) {
                            let message = '';
                            $.each(result.summary, function (i, val) {
                                message += '<p>' + val + '</p>';
                            });
                            success_message(message);

                        } else {
                            info_message(result.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorJson) {
                        helpers.btn_enable(btn);
                        alert(jqXHR.responseText);
                        //location.reload();
                    }
                });
                e.preventDefault();
                return false;
            });

            $('#btn_planning_center_disconnect').on('click', function (e) {
                let btn = helpers.btn_disable(this);
                $.ajax({
                    url: base_url + 'integrations/planningcenter/disconnect', type: "GET",
                    dataType: "json",
                    success: function (result) {
                        helpers.btn_enable(btn);
                        if (result.status) {
                            $('.btn_planning_center_push').hide();
                            $('.btn_planning_center_oauth_conn').show('fast');                            
                        }
                    },
                    error: function (jqXHR, textStatus, errorJson) {
                        helpers.btn_enable(btn);
                        alert(jqXHR.responseText);
                        //location.reload();
                    }
                });
                e.preventDefault();
                return false;
            });

            //==== Organization Changed
            async function loadSubOrganizations () {
                var selectInput = $('select[name="suborganization_id"]');
                var organization_id = $('select[name="organization_id"]').val();
                $('select[name="suborganization_id"]').empty();
                $('select[name="suborganization_id"]').append($('<option/>',{value:''}).html('Select a Sub Organization'));
                if(organization_id){
                    //Set Sub Organizations to Datatable Filters
                    await $.post(base_url + 'suborganizations/get_suborganizations_list', {organization_id:organization_id} , function (result) {
                        for (var i in result) {
                            selectInput.append($('<option/>'
                                ,{value: result[i].id,'data-token': result[i].token})
                                .html(result[i].name));
                        }
                    }).fail(function (e) {
                        console.log(e);
                    });
                }
            }

            loadSubOrganizations();

            //Get Settings with Organization Id
            $('select[name="organization_id"]').change(loadSubOrganizations);

            //Download Wordpress Link
            $('#download_wordpress_plugin').click(function (e) {
                e.preventDefault();
                var organization_id = $('select[name="organization_id"]').val();
                if(organization_id) {
                    var suborganization_id = $('select[name="suborganization_id"]').val();
                    var token = $('select[name="organization_id"] option:selected').data('token');
                    if(suborganization_id !== '') {
                        token = $('select[name="suborganization_id"] option:selected').data('token');
                    }
                    $.post(base_url + 'install/wordpress_download', {organization_id:organization_id,suborganization_id:suborganization_id,token:token}
                        , function (data) {
                            if (data.status === true) {
                                var file_path = data.data;
                                var a = document.createElement('A');
                                a.href = file_path;
                                a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                            }
                        }).fail(function (e) {
                        console.log(e);
                    });
                }
            });
        }};
}());

