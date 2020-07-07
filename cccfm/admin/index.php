<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$phpself = explode('/', $_SERVER['PHP_SELF']);
$username = $phpself[1];
$pices_url = explode("/", $url);
$username = end($pices_url);
if ($username == "") {
    $username = $pices_url[count($pices_url) - 2];
}
require_once '../general_function.php';
?>


<html lang="en"><!-- Mirrored from egemem.com/theme/kode/v1.1/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Apr 2015 09:26:17 GMT --><head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login</title>
        <link href="<?php echo base_url('assets/css/root.css'); ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/plugin/bootstrap-toggle/bootstrap-toggle.min.css'); ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/plugin/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css'); ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/plugin/sweet-alert/sweet-alert.css'); ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/plugin/date-range-picker/daterangepicker.css'); ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/plugin/bootstrap-timepicker/bootstrap-datetimepicker.css'); ?>" rel="stylesheet">
        <style type="text/css">
            .error1_msg{
                color: #E61818; text-align: center
            }
            .success1_msg{
                color:  #009900; text-align: center
            }

        </style></head>
    <body>

        <div class="login-form">
            <form method="post" action="<?php echo base_url('index.php/login/admin_do_login'); ?>">
                <div class="top">
                    <h1>Tap-In Here</h1>
                    <h4>Admin Login Area!!!</h4>
                </div>
                <div class="form-area">
                    <div class="group">
                        <input type="text" class="form-control" placeholder="Username" required id="username" name="username" value="<?php
                        echo $username;
                        ?>"  disabled>
                        <input type="hidden" class="form-control"  id="username" name="username" value="<?php
                        echo $username;
                        ?>"  >
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="group">
                        <input type="password" class="form-control" placeholder="Password" required  id="business_password" name="password" value="11152015">
<!--                        <input type="hidden"   id="business_url" name="business_url" value="--><?php //echo $url; ?><!--">-->
                        <i class="fa fa-key"></i>
                    </div>
                    <button type="submit" class="btn btn-default btn-block">LOGIN</button>
                </div>


            </form>

        </div>



        <script>
            window.history.forward(-1);
        </script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/webshim/minified/polyfiller.js'); ?>"></script>
        <script>
            webshim.activeLang('en');
            webshims.polyfill('forms');
            webshims.cfg.no$Switch = true;
        </script>


    </body>
</html>