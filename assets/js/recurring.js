(function () {
    $(document).ready(function () {
        subs.setsubs_dt();
    });
    var subs = {
        setsubs_dt: function () {
            var tableId = "#subscriptions_datatable";
            this.dtTable = $(tableId).DataTable({
                "dom": '<"row row_filter"<"col-md-9 col-sm-12 filter-1"><"col-md-3 col-sm-12 search"f>><"row"<"col-sm-12 filter-2">>rt<"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
                language: dt_language,
                processing: true, serverSide: true, aLengthMenu: [[10, 50], [10, 50]], order: [[0, "desc"]],
                ajax: {
                    url: base_url + "donations/get_subscriptions_dt", type: "POST",
                    "data": function (d) {
                        d.organization_id = $('select#organization_filter').val();
                        d.suborganization_id = $('select#suborganization_filter').val();
                        d.fund_id = $('select#fund_filter').val();
                        d.method = $('select#method_filter').val();
                        d.freq = $('select#freq_filter').val();
                    }
                },
                "fnPreDrawCallback": function () {
                    $(tableId).fadeOut("fast");
                },
                "fnDrawCallback": function (data) {
                    $(tableId).fadeIn("fast");

                    $("#subs_monthly_total").text('$' + data.json.include.subs_data.monthly.total);
                    $("#subs_monthly_max").text('$' + data.json.include.subs_data.monthly.max_net);
                    
                    $("#subs_all_total").text('$' + data.json.include.subs_data.all.total);
                    $("#subs_total_max").text('$' + data.json.include.subs_data.all.max_net);
                    
                    $("#subs_count_total").text(data.json.include.subs_data.count.count);
                    $("#subs_count_since").text(data.json.include.subs_data.count.since);

                    let search_value = $('#div_search_filter input').val();
                    if (typeof search_value !== "undefined" && search_value !== null && search_value.trim() !== '') {
                        $('#sub_totals_container').hide();
                    } else {
                        $('#sub_totals_container').show();
                    }
                },
                columns: [
                    {data: "id", className: "text-center", searchable: true},
                    {data: "id", className: "text-center", sortable: false, searchable: false, render: function (data, type, full) {
                            var stop_subscription = "";
                            if (full.status == 'A') {
                                stop_subscription = `<a class="stop_subscription dropdown-item"  data-id="` + data + `" href="#">
                                            <i class="fas fa-ban"></i>
                                            <span>Stop Subscription</span>
                                        </a>`;
                            }

                            if (stop_subscription != '')
                                return `<li class="nav-item dropdown" style="position: static">
                                      <a class="nav-link nav-link-icon" href="#" id="navbar-success_dropdown_1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                      </a>
                                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbar-success_dropdown_1">`
                                        + stop_subscription
                                        + `
                                      </div>
                                    </li>`;
                            return '...';
                        }
                    },
                    {data: "amount", className: "text-center", searchable: false, visible: true,
                        render: function (data, type, full) {
                            var recurring_icon = "";
                            var freq = "";
                            if (full.subscription != null && full.subscription > 0) {
                                recurring_icon = "<i class='fa fa-retweet'></i> ";
                                freq = full.subfrequency.charAt(0).toUpperCase() + full.subfrequency.slice(1); //ucfirst
                                freq = '<span style="color: darkgray; font-size: 12px; font-style:italic"> ' + freq + 'ly</span>';
                            }
                            return recurring_icon + '$' + (data ? data : 0.0) + freq;
                        }
                    },
                    {data: "trxs_count", className: "text-center", searchable: false},
                    {data: "fee", className: "text-center", searchable: false, visible: true, render: function (data) {
                            return '$' + (data ? data : 0.0);
                        }
                    },
                    {data: "given", className: "text-center", searchable: false,
                        render: function (data, type, full) {
                            return '<strong><span style="cursor: pointer" title="'
                                    + ''
                                    + '">$' + (data ? data : 0.0)
                                    + '</span><strong>';
                        }
                    },
                    {data: "frequency", className: "text-center", searchable: false},
                    {data: "name", className: "", searchable: true},
                    {data: "email", className: "", searchable: true},
                    {data: "fund", className: "", searchable: false,
                        render: function (data, type, full) {
                            let tfSubNet = '';
                            
                            if(full.tfSubNet != null) {
                            let tfSubNetArray = full.tfSubNet.split(', ');
                                tfSubNetArray.forEach(function(element) {
                                    tfSubNet += '$' + element + ', ';
                                });
                            }
                            
                            tfSubNet = tfSubNet != '' ? tfSubNet.slice(0, -2) : '';
                            
                            return '<span style="cursor: pointer" title="'
                                    + 'Distribution: ' + tfSubNet                                    
                                    + '">' + (data != null ? data : '[data_is_not_clean]')
                                    + '</span>';
                        }
                    },
                    {data: "method", className: "text-center", searchable: false},
                    {data: "status_text", className: "text-center", searchable: false, render: function (data, type, full) {
                            if (data == 'Active') {
                                return '<span class="badge badge-pill badge-default">&nbsp;' + data + '&nbsp;</span>';
                            } else {
                                return '<span class="badge badge-pill badge-warning">' + data + '</span>';
                            }

                        }
                    },
                    {data: "start_on", className: "text-center", searchable: false},
                    {data: "created_at", className: "text-center", searchable: false}                    
                ],
                fnInitComplete: function (data) {
                    helpers.table_filter_on_enter(this);
                    var search_filter = $('.search input');
                    $('#div_search_filter').append(search_filter);
                }
            });

            //Set Organizations to Datatable Filters
            $.post(base_url + 'organizations/get_organizations_list', function (result) {
                for (var i in result) {
                    var selectInput = $('select#organization_filter');
                    selectInput.append($('<option/>', {value: result[i].ch_id}).html(result[i].church_name));
                }
            }).fail(function (e) {
                console.log(e);
            });

            //Laading Dinamically Sub Organizations
            function loadDinamycSubOrganizations() {
                var selectInput = $('select#suborganization_filter');
                selectInput.empty();
                selectInput.append($('<option/>', {value: ''}).html('All Sub Organizations'));

                var organization_id = $('select#organization_filter').val();
                //Set Sub Organizations to Datatable Filters
                $.post(base_url + 'suborganizations/get_suborganizations_list', {organization_id: organization_id}, function (result) {
                    for (var i in result) {
                        selectInput.append($('<option/>', {value: result[i].id}).html(result[i].name));
                    }
                }).fail(function (e) {
                    console.log(e);
                });
            }
            loadDinamycSubOrganizations();

            //Laading Dinamically Funds
            function loadDinamycFunds() {
                var selectInput = $('select#fund_filter');
                selectInput.empty();
                selectInput.append($('<option/>', {value: ''}).html('All Funds'));

                var organization_id = $('select#organization_filter').val();
                var suborganization_id = $('select#suborganization_filter').val();
                //Set Sub Organizations to Datatable Filters
                $.post(base_url + 'funds/get_funds_list', {organization_id: organization_id, suborganization_id: suborganization_id}, function (result) {
                    for (var i in result) {
                        selectInput.append($('<option/>', {value: result[i].id}).html(result[i].name));
                    }
                }).fail(function (e) {
                    console.log(e);
                });
            }
            loadDinamycFunds();

            //Event Change Organization Filter
            $('select#organization_filter').change(async function () {
                await loadDinamycSubOrganizations();
                await loadDinamycFunds();
                subs.dtTable.draw(false);
            });

            //Event Change SubOrganization Filter
            $('select#suborganization_filter').change(async function () {
                await loadDinamycFunds();
                subs.dtTable.draw(false);
            });

            //Event Change Fund Filter
            $('select#fund_filter').change(function () {
                subs.dtTable.draw(false);
            });

            //Event Change Method Filter
            $('select#method_filter').change(function () {
                subs.dtTable.draw(false);
            });

            $('select#freq_filter').change(function () {
                subs.dtTable.draw(false);
            });

            //Event Stop Subscription
            $(document).on('click', '.stop_subscription', function (e) {
                var subscription_id = $(this).data('id');
                question_modal('Stop Subscription', 'Are you sure?')
                        .then(function (result) {
                            if (result.value) {
                                var data = $("#general_token_form").serializeArray();
                                var subscription_data = {};
                                $.each(data, function () {
                                    subscription_data[this.name] = this.value;
                                });
                                subscription_data['subscription_id'] = subscription_id;
                                loader('show');
                                $.ajax({
                                    url: base_url + 'donations/stop_subscription', type: "POST",
                                    dataType: "json",
                                    data: subscription_data,
                                    success: function (data) {
                                        if (data.status) {
                                            success_message(data.message);
                                        } else {
                                            error_message(data.message);
                                        }
                                        subs.dtTable.draw(false);
                                        typeof data.new_token.name !== 'undefined' ? $('input[name="' + data.new_token.name + '"]').val(data.new_token.value) : '';
                                        loader('hide');
                                    },
                                    error: function (jqXHR, textStatus, errorJson) {
                                        loader('hide');
                                        error_message(jqXHR.responseText);
                                    }
                                });
                            }
                        });
                e.preventDefault();
                return false;
            });
        }
    };
}());

