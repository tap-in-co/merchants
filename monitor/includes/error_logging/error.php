<?php
function pt_log_error($module, $err_kind, $err_msg, $logfile='') {
    $errorFileWithPath = ini_get('error_log');
    $pt_error_file = 'pt_php_error.log';
    if (strlen($logfile) > 4)
        $pt_error_file = $logfile;
    $pt_error_file = dirname($errorFileWithPath) . '/' . $pt_error_file;
    $time = time();
    $date = date("d-m-Y H:i", $time);
    $final_err_message = $err_kind . " " . $date . ' '. $module . " " . $err_msg . PHP_EOL;

    error_log($final_err_message, 3, $pt_error_file);
}

?>