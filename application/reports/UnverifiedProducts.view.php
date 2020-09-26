
<?php
    use \koolreport\widgets\koolphp\Table;
?>
<link href="<?php echo base_url('assets/css/root.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/plugin/bootstrap-toggle/bootstrap-toggle.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/plugin/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/plugin/sweet-alert/sweet-alert.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/plugin/date-range-picker/daterangepicker.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/plugin/bootstrap-timepicker/bootstrap-datetimepicker.css'); ?>" rel="stylesheet">
<!-- <div class="loading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" alt="loading-img"></div> -->
        <div id="top" class="clearfix">

            <!-- Start App Logo -->
            <div class="applogo">
                <a href="<?php echo base_url(); ?>" class="logo">Tap4Markets</a>
            </div>
            <!-- End Top Right -->
        </div>
<html>
<head>
    <title>Unverified Products</title>
    <?php include('v_header_report.php'); ?>
    <style>
        .cssHeader
        {
            background-color:rgb(244, 118, 40);
            font-size: 15px;
        }
        .cssItem
        {
            background-color:#fdffe8;
            font-size: 12px;
        }
    </style>
</head>
<body style="background-color:white;">

<div align="center">
     <h1>Tap4Markets</h1>
    <h3>Unverified Products Report</h3>
</div>
<form method="post">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">






        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="form-group">



            </div>
            <div class="form-group text-center">
                <button class="btn btn-success">
                    <i class="glyphicon glyphicon-refresh"></i> Load</button>
            </div>
        </div>
    </div>
</form>


<?php
Table::create(array(
    "dataStore"=>$this->dataStore("corp_orders"),
    "showFooter"=>true,
    "paging"=>array(
        "pageSize"=>40,
        "pageIndex"=>0,
        "align"=>"center"
    ),
    "class"=>array(
        "table"=>"table table-hover"
    ),
    "cssClass"=>array(
        "table"=>"table table-bordered",
        "th"=>"cssHeader",
        "tr"=>"cssItem"
    ),
    "columns"=>array(
        "Business Name"=>array(
            "cssStyle"=>"width:75px"
        ),
        "Product Name"=>array(
//                    "cssStyle"=>"font-size: 11px",
           "cssStyle"=>"width:120px"
        ),

        "Short Description"=>array(
    //                    "cssStyle"=>"font-size: 11px",
           "cssStyle"=>"width:200px"
        ),

            "Category"=>array(
        //                    "cssStyle"=>"font-size: 11px",
               "cssStyle"=>"width:50px"
            ),

                "Modification Time"=>array(
            //                    "cssStyle"=>"font-size: 11px",
                   "cssStyle"=>"width:50px"
                )

    )
));
?>
</body>
</html>