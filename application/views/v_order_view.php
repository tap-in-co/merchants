<div class="invoice invoice-row" tabindex="1">

    <div class="invoicename"><a href="#" onclick="PrintDiv()" class=" btn btn-primary"><i class="fa fa-print"></i>Print</a></div>
    <div id="printDiv">
        <div class="line row">
            <div class="col-md-6 padding-0 text-left">
                <h4 id="order_view_h4" >

                    <?php if ($orderlist[0]['status'] == "1") {
                        ?>     
                        <img   src="<?php echo base_url('assets/img/ic_error@3x.png'); ?>" alt="img" class="img order_view_icon">
                        NEW ORDER
                    <?php } elseif ($orderlist[0]['status'] == "2") {
                        ?>

                        <img src="<?php echo base_url('assets/img/ic_reload@3x.png'); ?>" alt="img" class="img order_view_icon">
                        In Progress
                    <?php } elseif ($orderlist[0]['status'] == "3") {
                        ?>

                        <img src="<?php echo base_url('assets/img/ic_check_active@3x.png'); ?>" alt="img" class="img order_view_icon">
                        Completed
                        <?php
                    }
                    ?>
                    <input type="hidden" id="orderstatus" value="<?php echo $orderlist[0]['status']; ?>">

                </h4>
                <h2>#<?php echo $orderlist[0]['order_id']; ?> <span class="lowlighter">For</span> <?php echo $orderlist[0]['nickname']; ?></h2>
                <span class="time">Ordered <?php echo time_elapsed_string($orderlist[0]['seconds']); ?></span>

                <br>
                <span class="note">Note: <?php echo $orderlist[0]['note']; ?></span>
            </div>
            <div class="col-md-6  padding-0 text-right">


            </div>
        </div>


        <input type="hidden" name="order_id" id="order_id" value="<?php echo encrypt_string($orderlist[0]['order_id']); ?>" />
        <table class="table" >
            <thead  class="title">
                <tr>
                    <td class="th_product">PRODUCT</td>
                    <td class="th_price" >PRICE</td>
                    <td class="th_quantity">QUANTITY</td>
                    <td class="th_total text-right">TOTAL</td>
                </tr>
            </thead>
            <tbody>

                <?php for ($i = 0; $i < count($order_detail); $i++) {
                    ?>
                    <tr>
                        <td class="th_product"><?php
                            echo $order_detail[$i]['name'];
                            foreach ($order_detail[$i]['option_ids'] as $option) {
                                ?>
                                <p class="product_description"><?php echo $option['name'] ?> </p>
                            <?php }
                            ?>
                        </td>
                        <td class="th_price" >$ <?php echo $order_detail[$i]['price']; ?></td>
                        <td class="th_quantity"><?php echo $order_detail[$i]['quantity']; ?></td>
                        <td class="th_total text-right">$ <?php
                            $per_item_total[$i] = $order_detail[$i]['price'] * $order_detail[$i]['quantity'];
                            echo number_format((float) $per_item_total[$i], 2, '.', '')
                            ?></td>
                    </tr>


                <?php }
                ?>

                <tr>
                    <td>Subtotal</td>
                    <td colspan="3" class="text-right "><h6 class="total">$ <?php echo $orderlist[0]['subtotal']; ?></h6></td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td colspan="3" class="text-right"><h6 class="total">$ <?php echo $orderlist[0]['tax_amount']; ?></h6></td>
                </tr>
                <tr>
                    <td>Tip</td>
                    <td colspan="3" class="text-right"><h6 class="total">$ <?php echo $orderlist[0]['tip_amount']; ?></h6></td>
                </tr>
                <tr>
                    <td>Points</td>
                    <td colspan="3" class="text-right"><h6 class="total">$ <?php echo $orderlist[0]['points_dollar_amount']; ?></h6></td>
                </tr>


                <tr>
                    <td>TOTAL</td>                    
                    <td colspan="3" class="text-right "><h4 class="total"><input type="hidden" name="order_amount" id="order_amount" value="<?php echo $orderlist[0]['total']; ?>"  /> $ <?php echo $orderlist[0]['total']; ?></h4></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>

        <div class="bottomtext" style="text-align: center ">
            <?php if ($orderlist[0]['status'] == "1") {
                ?>
                <a id="button_approve" href="#" class=" btn btn-primary " data-toggle="modal" data-target="#approveModal" style=" font-size: 20px;">
                    APPROVE
                </a>
                <script>
                    document.querySelector('#button_approve').onclick = function() {
                        $("#button_approve").html('APPROVE..');
                        var order_id = $("#order_id").val();
                        var amout = $("#order_amount").val();
                        var param = {order_id: order_id};
                        $.post("<?php echo base_url('index.php/site/payment') ?>", param)
                                .done(function(data) {
                                    data = jQuery.parseJSON(data);
                                    if (data['status'] == '1')
                                    {
                                        $("#button_approve").remove();
                                        $("#button_reject").remove();
                                        $("#button_complete").show();
                                        $("#order_view_h4").html('<img src="<?php echo base_url('assets/img/ic_reload@3x.png'); ?>" alt="img" class="img order_view_icon"> In Progress');
                                        $("#order_id_" + order_id + " .img").remove("asasas");
                                        $("#order_id_<?php echo $orderlist[0]['order_id']; ?> img").attr('src', "<?php echo base_url('assets/img/ic_reload@3x.png'); ?>");
                                        $("#li_order_id_<?php echo $orderlist[0]['order_id']; ?>").removeClass('pending_order_color');
                                        $("#order_id_<?php echo $orderlist[0]['order_id']; ?> img").toggleClass('pending_order_img img');
                                        swal("$" + data['amount'], "Your payment has been successfully processed", "success");

                                    } else {
                                        $("#button_approve").html('APPROVE');
                                        swal("$" + amout, data['msg'], "error");
                                    }
                                });
                    };
                </script>
                &nbsp;
                &nbsp;
                &nbsp;

                <a id="button_reject" href="#" class=" btn btn-danger " style=" font-size: 20px;">
                    REJECT
                </a>
                <script>
                    document.querySelector('#button_reject').onclick = function() {
                        $("#button_reject").html('REJECT..');
                        var order_id = $("#order_id").val();
                        var param = {order_id: order_id};
                        $.post("<?php echo base_url('index.php/site/rejectorder') ?>", param)
                                .done(function(data) {
                                    data = jQuery.parseJSON(data);

                                    if (data['status'] == '1')
                                    {
                                        $("#order_view").html('');
                                        $("#order_id_<?php echo $orderlist[0]['order_id']; ?>").remove();
                                        swal("Rejected", "Your order has been successfully rejected", "success");
                                    }
                                });
                    };
                </script>

                <a id="button_complete" href="#" class=" btn btn-primary " data-toggle="modal" data-target="#completeModal"    style=" font-size: 20px; display: none">
                    COMPLETE
                </a>
                <script>
                    document.querySelector('#button_complete').onclick = function() {
                        $("#button_complete").html('COMPLETE..');
                        var order_id = $("#order_id").val();
                        var param = {order_id: order_id};
                        $.post("<?php echo base_url('index.php/site/completedorder') ?>", param)
                                .done(function(data) {
                                    data = jQuery.parseJSON(data);
                                    if (data['status'] == '1')
                                    {
                                        $("#button_complete").remove();
                                        swal("Completed order", "", "success");
                                        $("#order_view_h4").html('<img src="<?php echo base_url('assets/img/ic_check_active@3x.png'); ?>" alt="img" class="img order_view_icon"> Completed');
                                        $("#order_id_<?php echo $orderlist[0]['order_id']; ?> img").attr('src', "<?php echo base_url('assets/img/ic_check_active@3x.png'); ?>");
                                        setInterval(function() {
                                            $("#order_id_<?php echo $orderlist[0]['order_id']; ?>").remove();
                                        }, 2000)

                                    } else {
                                        $("#button_complete").html();
                                        swal("Completed order", data['msg'], "error");
                                    }
                                });


                    };
                </script>


            <?php } elseif ($orderlist[0]['status'] == "2") {
                ?>
                <a id="button_complete" href="#" class=" btn btn-primary " data-toggle="modal" data-target="#completeModal"    style=" font-size: 20px;">
                    COMPLETE
                </a>
                <script>
                    document.querySelector('#button_complete').onclick = function() {
                        $("#button_complete").html('COMPlETE..');
                        var order_id = $("#order_id").val();
                        var param = {order_id: order_id};
                        $.post("<?php echo base_url('index.php/site/completedorder') ?>", param)
                                .done(function(data) {
                                    data = jQuery.parseJSON(data);
                                    if (data['status'] == '1')
                                    {
                                        $("#button_complete").remove();
                                        swal("Completed order", "", "success");
                                        $("#order_view_h4").html('<span><img src="<?php echo base_url('assets/img/ic_check_active@3x.png'); ?>" alt="img" class="img order_view_icon"></span> Completed');
                                        $("#order_id_<?php echo $orderlist[0]['order_id']; ?> img").attr('src', "<?php echo base_url('assets/img/ic_check_active@3x.png'); ?>");

                                        setInterval(function() {
                                            $("#order_id_<?php echo $orderlist[0]['order_id']; ?>").remove();
                                        }, 2000)

                                    } else {
                                        $("#button_complete").html();
                                        swal("Completed order", data['msg'], "error");
                                    }
                                });
                    };

                </script>



            <?php }
            ?>


        </div>

    </div>
</div>

<script type="text/javascript">
    function PrintDiv() {

        var divToPrint = document.getElementById('printDiv');
        var content = divToPrint.innerHTML.replace(/<img[^>]*>/g, "");

        var order_status = $('#orderstatus').val();
        console.log(order_status)
        if (order_status == 1)
        {
            $("#button_approve").html('APPROVE..');
            var order_id = $("#order_id").val();
            var amout = $("#order_amount").val();
            var param = {order_id: order_id};
            $.post("<?php echo base_url('index.php/site/payment') ?>", param)
                    .done(function(data) {
                        data = jQuery.parseJSON(data);
                        if (data['status'] == '1')
                        {
                            $("#button_approve").remove();
                            $("#button_reject").remove();
                            $("#button_complete").show();
                            $("#order_view_h4").html('<img src="<?php echo base_url('assets/img/ic_reload@3x.png'); ?>" alt="img" class="img order_view_icon"> In Progress');
                            $("#order_id_" + order_id + " .img").remove("asasas");
                            $("#order_id_<?php echo $orderlist[0]['order_id']; ?> img").attr('src', "<?php echo base_url('assets/img/ic_reload@3x.png'); ?>");
                            $("#li_order_id_<?php echo $orderlist[0]['order_id']; ?>").removeClass('pending_order_color');
                            $("#order_id_<?php echo $orderlist[0]['order_id']; ?> img").toggleClass('pending_order_img img');
                            swal("$" + data['amount'], "Your payment has been successfully processed", "success");
                            print_html(content)
                        } else {
                            $("#button_approve").html('APPROVE');
                            swal("$" + amout, data['msg'], "error");
                        }
                    });
        } else
        {
            print_html(content)
        }




        return false;

    }

    function print_html(content)
    {
        var frame1 = document.createElement('iframe');
        frame1.name = "frame1";
        frame1.style.position = "absolute";
        frame1.style.top = "-1000000px";
        document.body.appendChild(frame1);
        var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
        frameDoc.document.open();
        frameDoc.document.write('<html><link media="print" href="<?php echo base_url('assets/css/root.css'); ?>" rel="stylesheet"></link><style>@media print {  * { margin: 0 !important; padding: 8px !important; }}@media print   { .invoice .logo{ font-size:7pt;padding:0}} @media print   { .time { font-size:7px;}} @media print   { .note { font-size:7px;line-height:0}} @media print  {.invoice .line h2 { font-size:10px;line-height:0} } @media print  {.invoice .line h4 { font-size:7px} } @media print  { table { page-break-inside : auto }}@media print  { table tr td {  font-size: 5pt;}}@media print  { table .th_total { white-space: nowrap;}}@media print  { table .th_price { white-space: nowrap;}}@media print   {.invoice .table p { font-size:7px;line-height:0}}  </style><body onload="window.print()"><div class="invoice invoice-row"><div class="logo"><h1><?php echo $this->session->userdata('name'); ?></h1> </div>' + content + '</div></html>');
        frameDoc.document.close();
    }


</script>


