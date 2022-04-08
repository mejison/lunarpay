(function () {
    loader('show');
    $(document).ready(function () {
        referals.setDt();
        loader('hide');
    });
    var referals = {
        htmlCont: '#referals-container',
        tableId: "#referals_datatable",
        dtTable: null,
        setDt: function () {
             
            this.dtTable = $(referals.tableId).DataTable({
                "dom": '<"row"<"col-sm-9 filter-zone"><"col-sm-3 search"f>>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                language: dt_language,
                processing: true, serverSide: true, aLengthMenu: [[10, 50], [10, 50]], order: [[0, "desc"]],
                ajax: {
                    url: base_url + "settings/referals_get_dt", type: "POST",
                    "data": function (d) {
                      
                    }
                },
                "fnPreDrawCallback": function () {
                   
                },
                "fnDrawCallback": function () {
                     
                },
                columns: [
                    {data: "email",className: "text-center"},
                    {data: "full_name",className: "text-center"},
                    {data: "date_sent", className: "text-center", sortable: true, mRender: function (data, type, full) {
                        return full.date_sent_format;
                        }
                    },
                    {data: "date_register", className: "text-center", sortable: true, mRender: function (data, type, full) {
                        return full.date_register_format;
                        }
                    },
                     
                ],
                fnInitComplete: async function () {
                    helpers.table_filter_on_enter(this);
                   
                }
            });
          
           $(referals.htmlCont + ' .btn-add-referal-component').on('click', function (e) {
                $("#newReferal").modal("show");
            });
            $(referals.htmlCont + ' #referal-send').on('click', function (e) {
                
                $.post(base_url + 'referals/save',{
                    email:$("#referal-email").val(),
                    full_name:$("#referal-name").val(),
                    referal_message:$("#referal-message").val(),
                    csrf_token : $("input[name=csrf_token]").val()
                } , function (result) {
                    console.log(result);
                    if(result.status){
                        notify({title: 'Notification', 'message': 'We send you code successfully'});
                        setTimeout(()=>window.location.reload(),1500)  
                    }else if (!result.status) {
                        $("input[name=csrf_token]").val(result.new_token.value)
                        error_message(result.errors);
                    }
                }).fail(function (e) {
                    if (typeof e.responseJSON.csrf_token_error !== 'undefined' && e.responseJSON.csrf_token_error) {
                        alert(e.responseJSON.message);
                        window.location.reload();
                    }
                });
            });  
        } 
    };
}());

