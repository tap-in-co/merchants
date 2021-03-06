<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="keywords" content="" />
        <title>Tap In </title>
        <?php $this->load->view('v_head'); ?>
        <style>
            .add_product_btn{
                float: right;
                color: white !important;
            } 
        </style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">  
    </head>
    <body>
        <?php $this->load->view('v_header'); ?>
        <div class="content">
            <div class="container-mail">
                <div class="mailbox clearfix">
                    <div class="container-mailbox">



                        <div class="col-md-12">
                            <div class="panel panel-default">

                                <div class="panel-title">
                                    Option
                                    <a href="<?php echo base_url('index.php/option/add'); ?>" class="btn btn-primary add_product_btn"><i class="fa fa-plus"></i>Add</a>
                                </div>


                                <div class="panel-body table-responsive">

                                    <table class="table table-striped table-bordered ">
                                        <thead>
                                            <tr>
                                                <td>Option ID</td>
                                                <td>Name</td>
                                                <td>Price</td>
                                                <td>Description</td>
                                                
                                                
                                                <td>Category</td>
<td>AVAILABILITY STATUS</td>
                                                <td class="text-center">Edit</td>

                                            </tr>
                                        </thead>
                                        <tbody  id="option_tbody" >
                                            <?php for ($i = 0; $i < count($options); $i++) {
                                                ?>
                                                <tr id="option_tr_<?php echo $options[$i]['option_id']; ?>" >
                                                    <td># <b><?php echo $options[$i]['option_id']; ?></b></td>
                                                    <td><?php echo $options[$i]['name']; ?></td>
                                                    <td>$ <?php echo $options[$i]['price']; ?></td>
                                                    <td><?php echo $options[$i]['description']; ?></td>
                                                    <td><?php echo $options[$i]['product_option_category_name']; ?></td>
                                                     <td>
                                                        <?php
                                                        $checked = '';
                                                        if ($options[$i]['availability_status'] == 1) {
                                                            $checked = 'checked';
                                                        }
                                                        ?>
                                                        <div class="col-sm-8">
                                                            <input type="checkbox"  data-toggle="toggle" data-onstyle="success" id="availability_status_<?php echo $options[$i]['option_id']; ?>" onchange="set_availailblity_status(<?php echo $options[$i]['option_id']; ?>)" <?php echo $checked; ?> >
                                                        </div>

                                                    </td>
                                                    <!--<td><a href="<?php // echo base_url('index.php/product/options/' . $products[$i]['product_id']);  ?>" class="btn btn-info add_product_btn"><i class="fa fa-eye"></i>View</a></td>-->
                                                    <td><a href="<?php echo base_url('index.php/option/edit/' . $options[$i]['option_id']); ?>" class="btn btn-info add_product_btn"><i class="fa fa-edit"></i>Edit</a></td>
                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

            </div>
            <?php $this->load->view('v_footer'); ?>

        </div>




        <?php $this->load->view('v_script'); ?>


        <script>
            window.history.forward(-1);
            $("#option_tab").addClass('active_tab');
        </script>

               <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script> 


        <script>
   

            function set_availailblity_status(option_id) {
                var availability_status = $('#availability_status_' + option_id).prop('checked');
                var param = {availability_status: availability_status, option_id: option_id};
                $.post("<?php echo base_url('index.php/option/set_availailblity_status') ?>", param)
                        .done(function(data) {
                            data = jQuery.parseJSON(data);

                            if (data['status'] == '1')
                            {
                                swal("", data['msg'], "success");
                            }
                        });
            }

        $('#option_tbody').sortable({
            update:  function (event, ui) {
              var option_order=[];
              $('#option_tbody  tr').each(function (row) {

               var myarr = this.id.split("_");
               var rowID = myarr[myarr.length - 1];
               if(rowID!=""){
                   option_order.push(rowID);
               }

           });


              var param={'option_list':option_order.join()};
              $.post("<?php echo base_url('index.php/option/set_option_order') ?>", param)
              .done(function(data) {
                data = jQuery.parseJSON(data);

                if (data['status'] == '1')
                {
                    swal("", data['msg'], "success");
                }
            });


          }
      }
      );
        </script>






    </body>
</html>