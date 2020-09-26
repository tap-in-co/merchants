<?php date_default_timezone_set('America/Los_Angeles'); ?>
<!DOCTYPE html>
<html lang="en" class="jumbotron green">
<head>

            <li ><a id="reports_tab2" href="<?php echo base_url('index.php/AdminOrderReport'); ?>">Admin Report</a></li>
            <li ><a id="reports_tab1" href="<?php echo base_url('index.php/CorpDriverReport'); ?>">Pickup Report</a></li>
            <li ><a id="reports_tab2" href="<?php echo base_url('index.php/UnverifiedProductsReport'); ?>">Unverified Products</a></li>
            <li ><a id="reports_tab2" href="http://tapin-servers.dv/monitor">Monitor Orders</a></li>



    <title>Tap4Markets Monitor Orders</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Bootstrap core CSS -->
    <link href="./lib/external//bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <!-- <link href="new.css" rel="stylesheet"> -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="./lib/external//jqwidgets/styles/jqx.base.css" type="text/css" />
    <link rel="stylesheet" href="./lib/external//jqwidgets/styles/jqx.energyblue.css" type="text/css" />
    <!-- <script type="text/javascript" src="../lib/datejs-read-only/build/date.js"></script> -->
    <script type="text/javascript" src="./lib/external//scripts/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxgrid.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxdata.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxdatetimeinput.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxdropdownlist.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxwindow.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxinput.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxcalendar.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/globalization/globalize.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxmenu.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxlistbox.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxgrid.columnsresize.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxgrid.filter.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxgrid.sort.js"></script>
    <!-- <script type="text/javascript" src="./jqwidgets/jqxgrid.pager.js"></script> -->
    <!-- <script type="text/javascript" src="./jqwidgets/jqxgrid.grouping.js"></script> -->
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxdata.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxgrid.selection.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxcheckbox.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxgrid.edit.js"></script>
    <script type="text/javascript" src="./lib/external//jqwidgets/jqxgrid.aggregates.js"></script>
    <!-- <script type="text/javascript" src="./lib/external//jqwidgets/generatedata.js"></script> -->
    <script type="text/javascript">

        var page_theme = 'energyblue';
        // global variable to be used accress functions - i know it is bad but what to do?
        var search_selected_cell_new_value;
        var search_selected_cell_old_value;
        var search_selected_cell_data;

        var orders_selected_row_data;
        var orders_selected_row_index;
        var orders_selected_cell_old_value;
        var orders_selected_cell_new_value;

        var valid_update = true;
        var update_error_message = "";
        var changing_phone_number = 0;
        var changing_update_fields = 0;

        var dashboard_timer =setInterval(function(){ dashboard_info() }, 60000);

        function dashboard_info() {
            var database_API = get_controller_url();
            $.getJSON( database_API,
                {
                    cmd: "customer_support_utility_dashboard"
                }, function(result) {
                    display_dashboard_info(result);
              });
        }
        // dashboard_info().done (display_dashboard_info());

        function display_dashboard_info(result) {
            var text ='';
            $.each (result, function(idx, row) {
                text = text + row.name +": <font color = \"RED\">" + row.value + '</font>  ';
            })

            document.getElementById("dashboard_section").innerHTML = text;
        }
        function get_controller_url() {
            return ('./includes/model/model.php');
        }

        function delete_all_archive() {
            var ans = confirm ('Are you sure you want to delete all archived information?');
            if (!ans) {
                return;
            }
            var database_API = get_controller_url();
            var data = {
                cmd: "delete_all_archive"
            };
            $.getJSON( database_API,
                {
                    cmd: "delete_all_archive"
                }, function(result) {
                    alert("All archived information is deleted");

                }).fail(function(d) {
                    alert("Not all of the archived information could be deleted!")
                }).done(function(d) {
                });
        }
        function unarchive_all() {
            var ans = confirm ('Are you sure you want to un-archive all information?');
            if (!ans) {
                return;
            }
            var database_API = get_controller_url();
            var data = {
                cmd: "delete_all_archive"
            };
            $.getJSON( database_API,
                {
                    cmd: "unarchive_all"
                }, function(result) {
                    alert("All archived information is un-archived");

                }).fail(function(d) {
                alert("Not all of the archived information could be un-archived!")
            }).done(function(d) {
            });
        }


        function check_hidden(searchType, datafield) {
            // nothing to hide
            return false;
        }

        function cellclass(row, columnfield, value, row_data) {
            var mark_info = row_data.updated_fields;
            if (mark_info) {
                return 'red';
            }
            else {
                return;
            }
        }

        function sposor_cellclass(row, columnfield, value, row_data) {
            var mark_info = row_data.sponsor;
            if (mark_info == "1") {
                return 'blue';
            }
            else {
                return;
            }
        }

        //function buttonrenderer(row, column, value, defaultHtml) {
        function buttonrenderer(row, column, value, defaultHtml, columnSettings, record) {
            //return '<input type="button" onClick="updated_button_clicked(record)" value="Updated"/>';
        }

        function util_date_to_string(date)
        {
            var date_format = date.toString("MM/dd/yyyy");
            return date.format;
        }

        function field_valid_to_update(grid, event) {
            var valid_to_update_fields_search = [];
            var valid_to_update_fields_batch = ["workstep_name", "inventory_location_name"];
            var valid_to_update_fields;

            if (grid === "search_result") {
                valid_to_update_fields = valid_to_update_fields_search;
            } else {
                valid_to_update_fields = valid_to_update_fields_batch;
            }

            var valid = false;
            update_error_message = event.args.datafield + " field cannot be updated!";

            var validToUpdatefields = valid_to_update_fields.length;
            for (var i = 0; i < validToUpdatefields; i++) {
                if (valid_to_update_fields[i] == event.args.datafield) {
                    valid = true;
                    update_error_message = "";
                    break;
                }
            } // for

            return valid;
        }//function

        function field_value_valid_to_update(grid_name, event) {
            var date_pickup_date;
            var date_delivery_date;
            if ((event.args.datafield == "inventory_time") || (event.args.datafield == "end_time")) {
                var row = event.args.rowindex;
                var datarow = $(grid_name).jqxGrid('getrowdata', row);

                if (event.args.datafield == "inventory_time") {
                    date_pickup_date = event.args.value;
                    date_delivery_date = datarow.end_time;
                } else {
                    date_pickup_date = datarow.pickup_date;
                    date_delivery_date = event.args.value;
                }

                date_pickup_date = new Date(date_pickup_date.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3") );
                date_delivery_date = new Date(date_delivery_date.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3") );

                if (date_delivery_date < date_pickup_date) {
                    valid_update = false;
                    if (event.args.datafield == "start_time")
                        update_error_message = "Inventory date is after the End date.  You might want to change the End date first.";
                    else
                        update_error_message = "End date is before the Inventory date.";
                }
            }

            return valid_update;
        }//function

        function writeSearchHeader(searchType) {
            var textSearchType;
            var additional_info ="";

            for (var i =0; i < 5; i++) {
                switch(i) {
                    case 0:
                        if (searchType == "searchByOrderDate") {
                            textSearchType = "by order date";
                        }
                        break;
                    case 1:
                        if (searchType == "info_for_job_id") {
                            textSearchType = "by job ID";
                        }
                        break;
                    case 2:
                        if (searchType == "like_consumer_nickname") {
                            textSearchType = "nickname (similar)";
                        }
                        break;
                    case 3:
                        if (searchType == "searchNonProcessedUsers") {
                            textSearchType = "for new @home customers";
                        }
                        break;
                    case 4:
                        if (searchType == "searchUpdatedUsers") {
                            textSearchType = "for @home updates";
                        }
                        break;

                    default:
                        textSearchType = "";
                        additional_info = "";
                } //switch
            } // for
            document.getElementById('search_result_header').innerHTML = "Result for search "+ textSearchType;
            document.getElementById('additional_info').innerHTML = additional_info;
        }

        function search(searchType, searchCriteria)
        {
            // initializing global order grid variables
            $("#search_result").show();
            $("#search_for_corp").hide();
            $('#search_result').jqxGrid('clearselection');
            orders_selected_row_data = 0;
            orders_selected_row_index = -1;
            orders_selected_cell_old_value = 0;
            orders_selected_cell_new_value = 0;

            search_selected_cell_new_value = 0;
            search_selected_cell_old_value = 0;
            search_selected_cell_data = 0;
            changing_update_fields = 0;
            writeSearchHeader(searchType);

            // hide the order grid and header
            // document.getElementById('order_history_div').style.display='none';
            $('#search_result').jqxGrid('clear');
            $('#search_for_corp').jqxGrid('clear');
            $('#batches_grid').jqxGrid('clear');

            // prepare the data
            var theme = theme;
            var pathname = window.location.pathname;
            var urlpathname      = window.location.href;
            var url = get_controller_url() + "?cmd=" + searchType + searchCriteria;
            var source =
                {
                    datatype: "json",
                    default_value: {name:'true'},
                    datafields: [
                        // { name: 'is_active', type: 'checkbox'},
                        { name: 'corp_name', type: 'string' },
                        { name: 'vendor_name', type: 'string' },
                        { name: 'order_id', type:'int'},
                        { name: 'nickname', type: 'string' },
                        { name: 'cc_last_4_digits', type: 'string' },
                        { name: 'email1', type: 'string' },
                        { name: 'sms_no', type: 'string' },
                        { name: 'status', type:'int'},
                        { name: 'order_date', type:'date'},
                        { name: 'modify_date', type:'date'},
                        // { name: 'end_time', type:'string'},
                        { name: 'note', type:'string'},
                        { name: 'reject_reason', type:'string'},
                        { name: 'payment_processor_message',type:'string'},
                        { name: 'points_dollar_amount', type: 'string' },
                        { name: 'no_items', type: 'string'},
                        // { name: 'total_items', type:'string'},
                        { name: 'total', type: 'string' },
                        // { name: 'price_per_item', type: 'string' }
                    ],
                    //root: 'root',
                    url:url,
                    //updaterow: function (rowid, rowdata, commit, event) {
                    updaterow: function (rowid, rowdata, commit, event) {
                        // fornow
                        alert ("Cannot change any fields in the search result!");
                        $("#search_result").jqxGrid('setcellvalue', event.args.rowindex, search_selected_cell_new_value, search_selected_cell_old_value);
                        $('#search_result').jqxGrid('render');
                        return;

                        if (!valid_update) {
                            alert(update_error_message);
                            $("#search_result").jqxGrid('setcellvalue', event.args.rowindex, search_selected_cell_new_value, search_selected_cell_old_value);
                            $('#search_result').jqxGrid('render');
                            return;
                        }

                        var ans = confirm ('Are you sure you want to change the value for ' + args.datafield + ' from \"' + search_selected_cell_old_value + '\" to \"'+
                            search_selected_cell_new_value + '\" for '+ rowdata.home_phone+'?');
                        if (!ans) {
                            $("#search_result").jqxGrid('setcellvalue', event.args.rowindex, search_selected_cell_new_value, search_selected_cell_old_value);
                            $('#search_result').jqxGrid('render');
                            return;
                        }
                        // synchronize with the server - send update command
                        // processed means we have ported credit card information to spot also
                        if ( ($("#search_result").jqxGrid('getrowdata', rowid).processed === true) && (search_selected_cell_old_value === false) &&
                            (args.datafield === 'processed')){
                            $("#search_result").jqxGrid('getrowdata', rowid).updated_fields = '';
                            //$("#search_result").jqxGrid('setcellvalue', rowid, 'updated_fields', ''); // doesn't work inside update
                        }

                        var data="";
                        if (changing_phone_number) {
                            data = "cmd=update_phone_number";
                            data = data + "&new_phone_number='"+ search_selected_cell_new_value +"'" + "&old_phone_number='"  +  search_selected_cell_old_value + "'";
                        } else if (changing_update_fields) {
                            data = "cmd=unset_updated_fields";
                            data = data + "&phone_number='" + $("#search_result").jqxGrid('getrowdata', rowid).home_phone + "'" + "&updated_fields=" + search_selected_cell_new_value;
                        }
                        else {
                            data = "cmd=update_customer";
                            data = data + "&title='" + rowdata.title +"'" + "&first_name='"  + rowdata.first_name +"'" +
                                "&last_name='" + rowdata.last_name +"'";
                            data = data + "&zip_code='" + rowdata.zip_code + "'";
                            data = data + "&email='" + rowdata.email +"'";
                            data = data + "&phone_number='" + rowdata.home_phone +"'";
                            data = data + "&address_1='" + encodeURIComponent(rowdata.address_1) +"'";
                            data = data + "&address_2='" + encodeURIComponent(rowdata.address_2) +"'";
                            data = data + "&city='" + rowdata.city +"'";
                            data = data + "&s_d_i='" + encodeURIComponent(rowdata.s_d_i) + "'";
                            data = data + "&alldriver='" + encodeURIComponent(rowdata.alldriver) + "'";
                            data = data + "&processed=" + encodeURIComponent(rowdata.processed);
                            data = data + "&cc_number='" + rowdata.cc_number + "'";
                            data = data + "&cc_type='" + rowdata.cc_type + "'";
                            data = data + "&cc_expdate='" + rowdata.cc_expdate + "'"; // we don't need to send updated_fields to database, it always gets set to empty
                        }

                        $.ajax({
                            dataType: 'json',
                            url: get_controller_url(),
                            data: data,
                            success: function (data, status, xhr) {
                                // update command is executed.
                                // check error code: < 0 means didn't work
                                if (xhr.responseText >= 0) {
                                    $('#batches_grid').jqxGrid('updatebounddata');
                                    commit(true);
                                } else {
                                    alert("Update was not successful!");
                                }
                                $('#search_result').jqxGrid('updatebounddata');
                            },
                            error: function () {
                                // cancel changes.
                                commit(false);
                            }
                        });
                    }
                };
            var dataAdapter = new $.jqx.dataAdapter(source);
            $("#search_result").jqxGrid({
                width: '96%',
                // autoheight: true,
                height: 300,
                source: dataAdapter,
                theme: page_theme,
                columnsresize: true,
                selectionmode: 'singlecell',
                editable: true,
                sortable: true,
                filterable: true,
                showfilterrow: true,
                // rendered: function (type) {
                //     if (type == "rows") {
                //         var row_id = $("#search_result").jqxGrid('getrowid',0);
                //         $("#search_result").jqxGrid('setcellvalue', 1, 'first_name', 'PurpleTie');
                //     }
                // },
                columns: [
                    // { text: '', width: 70, columntype: 'button', cellsrenderer: function () {
                    //     return "View";
                    // }, buttonclick: function (row) {
                    //         // open the popup window when the user clicks a button.
                    //         editrow = row;
                    //         var offset = $("#search_result").offset();
                    //         $("#popupWindow").jqxWindow({ position: { x: parseInt(offset.left) + 60, y: parseInt(offset.top) - 100} });

                    //         // get the clicked row's data and initialize the input fields.
                    //         var dataRecord = $("#search_result").jqxGrid('getrowdata', editrow);
                    //         $("#updated_fields").val(dataRecord.updated_fields);
                    //         $("#first_name").val(dataRecord.first_name);
                    //         $("#last_name").val(dataRecord.last_name);
                    //         $("#phone").val(dataRecord.home_phone);
                    //         $("#email").val(dataRecord.email);
                    //         $("#address_1").val(dataRecord.address_1);
                    //         $("#address_2").val(dataRecord.address_2);
                    //         $("#city").val(dataRecord.city);
                    //         $("#zip_code").val(dataRecord.zip_code);
                    //         $("#cc_number").val(dataRecord.cc_number);
                    //         $("#cc_expdate").val(dataRecord.cc_expdate);
                    //         $("#s_d_i").val(dataRecord.s_d_i);
                    //         $("#alldriver").val(dataRecord.alldriver);
                    //         $("#timestamp").val(dataRecord.regist_timestamp);

                    //         // show the popup window.
                    //         $("#popupWindow").jqxWindow('open');
                    //         // $('#popupWindow').jqxGrid({ editable: false }); for some reason doesn't work
                    //     }
                    // },
                    // { text: 'Is active?', datafield: 'is_active', columntype: 'checkbox', width: 80, hidden:check_hidden(searchType, 'processed') },
                    // { text: 'CC info', datafield: 'updated', columntype: 'button', width: 90, hidden:check_hidden(searchType, 'updated'), cellsrenderer: function () {
                    //   return "Updated";}, buttonclick: function (row) {
                    //      var dataRecord = $("#search_result").jqxGrid('getrowdata', row);
                    //          // set global variables
                    //          search_selected_cell_old_value = dataRecord.updated_fields;
                    //          search_selected_cell_new_value = '';
                    //          valid_update = true;
                    //          changing_update_fields = 1;

                    //          dataRecord.updated_fields = '';
                    //          var rowID = $('#search_result').jqxGrid('getrowid', row);
                    //          $('#search_result').jqxGrid('updaterow', rowID, {updated_fields:''});
                    //      }
                    //  },
                    // { text: 'Updated fields', datafield: 'updated_fields', width: 200, hidden:check_hidden(searchType, 'updated_fields') },
                    // { text: 'Title', datafield: 'title', width: 60 },
                    { text: 'Market Name', datafield: 'corp_name', width: 180 },
                    { text: 'Vendor Name', datafield: 'vendor_name', width: 180 },
                    { text: 'Order ID', datafield: 'order_id', width: 80 },
                    { text: 'Status', datafield: 'status', width: 110, cellsformat: 'c2' },
                    { text: 'Consumer Nickname', datafield: 'nickname', width: 200 },
                    { text: 'Order date', datafield: 'order_date', width: 110, cellsformat: 'yyyy-MM-dd' },
                    { text: 'Modify date', datafield: 'modify_date', width: 110, cellsformat: 'yyyy-MM-dd' },
                    // { text: 'Due date', datafield: 'due_date', width: 110, cellsformat: 'yyyy-MM-dd' },
                    // { text: 'End date', datafield: 'end_time', width: 210 },
                    // { text: 'Product ID', datafield: 'product_id', width: 90 },


                    // { text: 'Credit card no.', datafield: 'cc_number', width: 150, hidden:check_hidden(searchType, 'cc_number'), cellclassname: cellclass },
                    // { text: 'Exp. date', datafield: 'cc_expdate', width: 70, hidden:check_hidden(searchType, 'cc_expdate'), cellclassname: cellclass },
                    // { text: 'Pickup date', datafield: 'cc_type', width: 100, hidden:check_hidden(searchType, 'cc_type'), cellclassname: cellclass },
                    { text: 'No. of items', datafield: 'no_items', width: 110 },
                    { text: 'Points value', datafield: 'points_dollar_amount', width: 110 },
                    { text: 'Note', datafield: 'note', width: 300 },
                    { text: 'Pickup location', datafield: 'pickup_location', width: 85 }

                    // { text: 'Total amount', datafield: 'total_value', width: 110, cellsformat: 'c2' },

                    // { text: 'Password', datafield: 'regist_password', width: 150, hidden: true}
                ]
            });

            // initialize the popup window and buttons.
            $("#popupWindow").jqxWindow({
                width: 750, resizable: false,  isModal: true, autoOpen: false,
                cancelButton: $("#Cancel"), modalOpacity: 0.3
            });
            $("#Cancel").jqxButton({ theme: theme });
            $("#Cancel").click(function () {
                /* disables the following alert -not needed and also was diplayed multiple times */
                /*alert("Please note no changes was saved - this popup window was view-only!");*/
                $("#popupWindow").jqxWindow('hide');
            });

            $("#search_result").on('cellvaluechanging', function (event) {
            });

            $("#search_result").on('cellbeginedit', function (event) {
                valid_update = field_valid_to_update("search_result", event);
                if (valid_update) {
                    search_selected_cell_old_value = event.args.value;
                    if (event.args.columntype == "checkbox")  // it is boolean new value will be negate of the old value
                        search_selected_cell_new_value = !search_selected_cell_old_value;
                }
            });
            $("#search_result").bind('cellselect', function (event) {
                var rowIndex = event.args.rowindex;
                //var datafield = event.args.datafield;
                search_selected_cell_data = $("#search_result").jqxGrid('getrowdata', rowIndex);
            });

            $("#search_result").on('cellendedit', function (event) {
                search_selected_cell_old_value = args.oldvalue;
                search_selected_cell_new_value = args.value;
                if (event.args.datafield == "home_phone") {
                    changing_phone_number = 1;
                }
                else {
                    changing_phone_number = 0;
                }
            });

            // events
            // $("#jqxgrid").bind('cellbeginedit', function (event) {
            //     var args = event.args;
            //     $("#cellbegineditevent").html("Event Type: cellbeginedit, Column: " + args.datafield + ", Row: " + (1 + args.rowindex) + ", Value: " + args.value);
            // });
            // $("#jqxgrid").bind('cellendedit', function (event) {
            //     var args = event.args;
            //     $("#cellendeditevent").html("Event Type: cellendedit, Column: " + args.datafield + ", Row: " + (1 + args.rowindex) + ", Value: " + args.value);
            // });

        } // search

        function getBatches()
        {
            // first show the order history header and grid
            // document.getElementById('order_history_div').style.display='block';
            // prepare the data
            if (!search_selected_cell_data) {
                alert("Please select an order in the search result.");
                return;
            }

            // document.getElementById('orders_header').innerHTML = "Batches for jobs "+ search_selected_cell_data.home_phone;
            document.getElementById('orders_header').innerHTML = "Items for Order ID: " + search_selected_cell_data.order_id;
            var theme = page_theme;
            var pathname = window.location.pathname;
            var urlpathname      = window.location.href;
            var url_params = "?cmd=info_for_order_id&order_id=" + search_selected_cell_data.order_id;
            var url = get_controller_url()+url_params
            var order_source =
                {
                    datatype: "json",
                    datafields: [
                        // { name: 'error_id', type: 'string' },
                        { name: 'order_id', type: 'string' },
                        // { name: 'shipping_job_id', type: 'string' },
                        // { name: 'product_id', type: 'string' },
                        // { name: 'batch_id', type: 'string' },
                        { name: 'product_name', type: 'string' },
                        { name: 'short_description', type: 'string' },
                        { name: 'option', type: 'string' },
                        { name: 'quantity', type: 'string' },
                        // { name: 'start_time', type: 'date' },
                        // { name: 'production_time', type: 'date' },
                        // { name: 'inventory_time', type: 'date' },
                        // { name: 'end_time', type: 'date' },
                        { name: 'points_dollar_amount', type: 'string' },
                        { name: 'price', type: 'string' },
                        { name: 'item_note', type: 'string' }
                    ],
                    //root: 'root',
                    url:url,
                    deleterow: function (rowid, commit) {
                        if (!orders_selected_row_data) {
                            alert("Please select an order");
                            return false;
                        }
                        var ans = confirm ('Are you sure you want to delete batch: '+ orders_selected_row_data.batch_id +'?');
                        if (!ans) {
                            return false;
                        }
                        var rowindex = $('#batches_grid').jqxGrid('getselectedrowindex');
                        // synchronize with the server - send delete command
                        // call commit with parameter true if the synchronization with the server is successful
                        //and with parameter false if the synchronization failed.
                        //var rowdata = $("#order_history").jqxGrid('getrowdata', rowid);
                        //TODO
                        var params = "cmd=delete_order&order_number=" + orders_selected_row_data.order_number;
                        $.ajax({
                            dataType: 'json',
                            url: get_controller_url(),
                            data: params,
                            success: function (data, status, xhr) {
                                // update command is executed.
                                commit(true);
                            },
                            error: function () {
                                // cancel changes.
                                commit(false);
                            }
                        });
                    }, // deleterow
                    updaterow: function (rowid, rowdata, commit, event) {
                        if (!valid_update) {
                            alert(update_error_message);
                            $("#batches_grid").jqxGrid('setcellvalue', event.args.rowindex, event.args.datafield, event.args.oldvalue);
                            $('#batches_grid').jqxGrid('render');
                            return;
                        }

                        var ans = confirm ('Are you sure you want to change \"' +  order_selected_cell_old_value + '\" to \"'+ order_selected_cell_new_value +'\"?');
                        if (!ans) {
                            $("#batches_grid").jqxGrid('setcellvalue', event.args.rowindex, event.args.datafield, event.args.oldvalue);
                            $('#batches_grid').jqxGrid('render');
                            return;
                        }
                        // synchronize with the server - send update command
                        //var order_date = util_date_to_string(rowdata.order_date);
                        var data = "cmd=update_batch_workstep_inventory&workstep='" + rowdata.workstep_name +"'" +"&batch_id='" + rowdata.batch_id +"'" + "&inventory_location_name='" + rowdata.inventory_location_name + "'";
                        //+ "&note=" + encodeURIComponent(rowdata.note);
                        $.ajax({
                            dataType: 'json',
                            url: get_controller_url(),
                            data: data,
                            success: function (data, status, xhr) {
                                // update command is executed.
                                commit(true);
                            },
                            error: function () {
                                // cancel changes.
                                commit(false);
                            }
                        });
                    }// update

                }; // source

            var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties) {
                // if (value != 0) {
                //     return '<span style="margin: 4px; float: ' + columnproperties.cellsalign + '; color: #0000ff;">' + value + '</ span>';
                // }
                // else {
                //     return '<span style="margin: 4px; float: ' + columnproperties.cellsalign + '; color: #008000;">' + value + '</ span>';
                // }
                if (value != 0) {
                    var element = $(defaulthtml);
                    element.css({ 'background-color': 'Yellow', 'width': '100%', 'height': '100%', 'margin': '0px' });
                    return element[0].outerHTML;
                }
                return defaulthtml;
            };
            var dataAdapter = new $.jqx.dataAdapter(order_source);
            $("#batches_grid").jqxGrid({
                width: '96%',
                autoheight: true,
                //height: 600,
                source: dataAdapter,
                theme: theme,
                columnsresize: true,
                statusbarheight: 50,
                showstatusbar: true,
                showaggregates: true,
                selectionmode: 'singlecell',
                editable: true,
                sortable: true,
                filterable: true,
                showfilterrow: true,
                columns: [
                    // { text: 'Product id', datafield: 'product_id', width: 110 },
                    //
                    // { text: 'Error code', datafield:'error_id', width: 80, cellsrenderer: cellsrenderer},
                    // { text: 'Job ID', datafield: 'job_id', width: 80 },
                    { text: 'Product name', datafield: 'product_name', width: 205 },
                    { text: 'Short description', datafield: 'short_description', width: 255 },
                    // { text: 'Batch id', datafield: 'batch_id', width: 90 },
                    // { text: 'Start date', datafield: 'start_time', cellsformat: 'yyyy-MM-dd', width: 120},
                    // { text: 'Production date', datafield: 'production_time', cellsformat: 'M-d-y HH:m', width: 120},
                    { text: 'Quantity', datafield: 'quantity', width: 70},
                    // { text: 'End date', datafield: 'end_time', width: 130},
                    // { text: 'No items in batch', datafield: 'batch_Qty', width: 130 },
                    // { text: 'Batch Value', datafield: 'batch_value', width: 135,
                    //     aggregates: ['count',
                    //         { 'Total value':
                    //             function (aggregatedValue, currentValue) {
                    //                 return aggregatedValue += currentValue;
                    //             }
                    //         }]
                    // },
                    { text: 'Price', datafield: 'price', width: 75 },
                    { text: 'Points value', datafield: 'points_dollar_amount', width: 90},
                    { text: 'Note', datafield: 'item_note', width: 210 },
                    // { text: 'Inventory location', datafield: 'inventory_location_name', width: 140 },
                    // { text: 'Shipping address', datafield: 'shipping_address', width: 160 },
                    // { text: 'Order date', datafield: 'order_date', width: 100, columntype: 'datetimeinput', cellsformat:'MM/dd/yyyy'},
                    // { text: 'Pickup date', datafield: 'pickup_date', width: 100, columntype: 'datetimeinput', cellsformat:'MM/dd/yyyy'},
                    // { text: 'Delivery date', datafield: 'delivery_date', width: 100, columntype: 'datetimeinput', cellsformat:'MM/dd/yyyy'},
                    // { text: 'Error message', datafield:'error_message', width: 200, cellsrenderer: cellsrenderer }
                ]
            });
            // delete row.
            $("#deleteOrderbutton").bind('click', function () {
                //var selectedrowindex = $("#order_history").jqxGrid('getselectedrowindex');
                // var rowscount = $("#order_history").jqxGrid('getdatainformation').rowscount;
                // var rows = $('#order_history').jqxGrid('getrows');
                // if (selectedrowindex >= 0 && selectedrowindex < rowscount)var commit = $("#jqxgrid").jqxGrid('deleterow', id); {
                //     var id = $("#order_history").jqxGrid('getrowid', selectedrowindex);
                //     var commit = $("#order_history").jqxGrid('deleterow', id);
                // }
                // var selectedrowindex = $("#order_history").jqxGrid('getselectedrowindex');
                //   var rowscount = $("#order_history").jqxGrid('getdatainformation').rowscount;
                //   if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                //       var id = $("#order_history").jqxGrid('getrowid', orders_selected_row_index);
                //       var commit = $("#jqxgrid").jqxGrid('deleterow', id);
                //   }

                if (orders_selected_row_index == -1)
                {
                    alert("please select an order again.");
                    return;
                }

                var id = $("#batches_grid").jqxGrid('getrowid', orders_selected_row_index);
                var commit = $("#batches_grid").jqxGrid('deleterow', id);
                $("#batches_grid").jqxGrid('unselectrow', id);
                $('#batches_grid').jqxGrid('clearselection');
                orders_selected_row_index = -1;
                orders_selected_row_data = 0;
            });
            $('#batches_grid').on('valueChanged', function (event)
            {
                var jsDate = event.args.date;
            });
            $("#batches_grid").bind('cellselect', function (event) {
                orders_selected_row_index = event.args.rowindex;
                //var datafield = event.args.datafield;
                orders_selected_row_data = $("#batches_grid").jqxGrid('getrowdata', orders_selected_row_index);
            });

            $("#batches_grid").on('cellvaluechanging', function (event) {
            });

            $("#batches_grid").on('cellendedit', function (event) {
                var row = args.rowindex;
                order_selected_cell_new_value = args.value;
                order_selected_cell_old_value = args.oldvalue;
                valid_update = field_value_valid_to_update("#batches_grid",event);
            });

            $("#batches_grid").on('cellbeginedit', function (event) {
                valid_update = field_valid_to_update("batches_grid", event);
            });
        }

        function makeArgs(arg) {
            return arg;
        }

    </script>
</head>
<body class="default">
<style>
    table
    {
        width: 750px;
    }
    td, th
    {
        width: 20%;
    }
    td
    {
        padding: 1px;
    }
    td:not(:first-child)
    {
        width: 80%;
    }
    input
    {
        width: 550px;
    }
    .yellow
    {
        color: black\9;
        background-color: yellow\9;
    }
    .dashboard
    {
        height: 30px;
        font-size: 17px;
        text-align: center;
        color: black;
        background-color: #f8f8f8;
    }
    .green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected)
    {
        color: white;
        background-color: #f6f7e8;
    }
    .blue:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .blue:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected)
    {
        color: white;
        background-color: #0000A0;
    }
    .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected)
    {
        color: black;
        background-color: yellow;
    }
    .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected)
    {
        color: black;
        background-color: #e83636;
    }

</style>
<body>
<body onload="dashboard_info();">
<div class="dashboard" id="dashboard_section"></div>
<div class="container">
    <div class="jumbotron">
        <h2>Search for jobs</h2>
    </div>
    <div class="row">
        <div class="col-md-3">
            <h3>By Order ID</h3>
            <div class="form-group">
                <!--<label class="control-label" for="formInput103">Phone number:&nbsp;</label>-->
                <input type="text" class="form-control" id="formInput102" placeholder="order id">
                <input type="hidden" name="args" value="@job_id" />
            </div>
            <button onclick="search('info_for_job_id', makeArgs('&amp;job_id=')+document.getElementById('formInput102').value)"> Search </button>
        </div>
        <div class="col-md-4">
            <h3>By Order Date</h3>
            <div class="form-group">
                <!-- <label class="control-label" for="formInput103">Order date:&nbsp;</label> -->
                <input type="text" class="form-control" id="formInput103" placeholder="yyyy-mm-dd">
            </div>
            <button onclick="search('searchByOrderDate', makeArgs('&amp;order_date=')+document.getElementById('formInput103').value)"> Search </button>
        </div>
        <div class="col-md-4">
            <h3>Nickname (similar)</h3>
            <div class="form-group">
                <!-- <label class="control-label" for="formInput17">customer name: &nbsp;</label> -->
                <input type="text" class="form-control" id="formInput17" placeholder="consumer's nickname">
            </div>
            <button onclick="search('like_consumer_nickname', makeArgs('&amp;customer_name=')+document.getElementById('formInput17').value)"> Search </button>
        </div>
        <BR>
        <BR>
    </div>
    <div class="jumbotron">
        <!-- <b><font size="6">Search result</font></b><i>,</i>,<u>,</u> -->
        <h2 id="search_result_header">Search result</h2>
    </div>
    <div class="row">
        <div style="margin-right: 15px;" class="">
            <div class="">

                <div id="additional_info" style="float: left; font-size: 13px; font-family: Verdana;margin-left: 25px;"></div>
                <div id="search_for_corp" style="font-size: 13px; font-family: Verdana;margin-left: 25px;"></div>
                <!-- <BR> -->
                <BR>
                <BR>
                <BR>
            </div>
            <div id="search_result" style="font-size: 13px; font-family: Verdana;margin-left: 20px;"></div>
        </div>
        <BR>
        <BR>
        <input style="margin-left: 25px;width:250px;min-width:250px;" id="show_batches" type="button" onclick="getBatches()" value="Items for selected Order ID" />
<!--        <input style="float: right; margin-right:25px; width:250px;min-width:250px;" id="unarchive_all" type="button" onclick="unarchive_all()" value="Unarchive all info" />-->
<!--        <input style="color:white;background-color:red;float: right; width:250px;min-width:250px;" id="delete_all_archive" type="button" onclick="delete_all_archive()" value="Delete all archived info. Caution!" />-->
        <BR>
        <BR>
        <BR>
        <BR>
        <div id="batches_div" class="">
            <div class="jumbotron">
                <h2 id="orders_header">Items</h2>
            </div>
            <div style="float: right; margin-right: 15px;">
            </div>
            <BR>
            <BR>
            <div id="batches_grid" style="font-size: 13px; font-family: Verdana;margin-left: 15px;"></div>
        </div>
    </div>
</div>
</div>
<div id="popupWindow" style="display:none">
    <div>View (Read/Copy Only): No changes will be saved on this screen.</div>
    <div style="overflow: hidden; background-color: #f8f8f8">
        <table>
            <tr>
                <td align="right">Updated fields:</td>
                <td align="left">
                    <input id="updated_fields" />
                </td>
            </tr>
            <tr>
                <td align="right">First Name:</td>
                <td align="left">
                    <input id="first_name" />
                </td>
            </tr>
            <tr>
                <td align="right">Last Name:</td>
                <td align="left">
                    <input id="last_name" />
                </td>
            </tr>
            <tr>
                <td align="right">Phone no:</td>
                <td align="left">
                    <input id="phone" />
                </td>
            </tr>
            <tr>
                <td align="right">Email:</td>
                <td align="left">
                    <input id="email">
                </td>
            </tr>
            <tr>
                <td align="right">Address 1:</td>
                <td align="left">
                    <input id="address_1">
                </td>
            </tr>
            <tr>
                <td align="right">Address 2:</td>
                <td align="left">
                    <input id="address_2">
                </td>
            </tr>
            <tr>
                <td align="right">City:</td>
                <td align="left">
                    <input id="city">
                </td>
            </tr>
            <tr>
                <td align="right">Zip code:</td>
                <td align="left">
                    <input id="zip_code">
                </td>
            </tr>
            <tr>
                <td align="right">CC number:</td>
                <td align="left">
                    <input id="cc_number">
                </td>
            </tr>
            <tr>
                <td align="right">CC expdate:</td>
                <td align="left">
                    <input id="cc_expdate">
                </td>
            </tr>
            <tr>
                <td align="right">Timestamp:</td>
                <td align="left">
                    <input id="timestamp">
                </td>
            </tr>
            <tr>
                <td align="right">Order instruction:</td>
                <td align="left">
                    <input id="s_d_i">
                </td>
            </tr>
            <tr>
                <td align="right">Driver instruction:</td>
                <td align="left">
                    <input id="alldriver">
                </td>
            </tr>
            <tr>
                <td align="right"></td>
                <td style="padding-top: 10px;" align="right">
                    <input style="margin-right: 50px; width: 150px" type="button" id="Cancel" value="Done" />
                    <!-- <input id="Cancel" type="button" value="Cancel" /></td> -->
            </tr>
        </table>
    </div>
</div>
<div id="workPopupWindow" style="display:none">
    <div>View (Read/Copy Only): No changes will be saved on this screen.</div>
    <div style="overflow: hidden; background-color: #f8f8f8">
        <table>
            <tr>
                <td align="right">Updated fields:</td>
                <td align="left">
                    <input id="atwork_updated_fields" />
                </td>
            </tr>
            <tr>
                <td align="right">First Name:</td>
                <td align="left">
                    <input id="atwork_first_name" />
                </td>
            </tr>
            <tr>
                <td align="right">Last Name:</td>
                <td align="left">
                    <input id="atwork_last_name" />
                </td>
            </tr>
            <tr>
                <td align="right">Phone no:</td>
                <td align="left">
                    <input id="atwork_phone" />
                </td>
            </tr>
            <tr>
                <td align="right">Email:</td>
                <td align="left">
                    <input id="atwork_email">
                </td>
            </tr>
            <tr>
                <td align="right">Spot group:</td>
                <td align="left">
                    <input id="atwork_address_1">
                </td>
            </tr>
            <tr>
                <td align="right">Sponsored:</td>
                <td align="left">
                    <input id="atwork_sponsored">
                </td>
            </tr>
            <tr>
                <td align="right">CC zip code:</td>
                <td align="left">
                    <input id="atwork_zip_code">
                </td>
            </tr>
            <tr>
                <td align="right">CC number:</td>
                <td align="left">
                    <input id="atwork_cc_number">
                </td>
            </tr>
            <tr>
                <td align="right">CC expdate:</td>
                <td align="left">
                    <input id="atwork_cc_expdate">
                </td>
            </tr>
            <tr>
                <td align="right">Order instruction:</td>
                <td align="left">
                    <input id="atwork_s_d_i">
                </td>
            </tr>
            <tr>
                <td align="right">Timestamp:</td>
                <td align="left">
                    <input id="atwork_regist_timestamp">
                </td>
            </tr>
            <tr>
                <td align="right"></td>
                <td style="padding-top: 10px;" align="right">
                    <input style="margin-right: 50px; width: 150px" type="button" id="WorkCancel" value="Done" />
                    <!-- <input id="Cancel" type="button" value="Cancel" /></td> -->
            </tr>
        </table>
    </div>
</div>
<!--         <div style="margin-left:68px;">
    <a href="./deliveryreport.php" target="_blank"></a>
  </div> -->
</body>
</html>
