<?php

function error_res($msg) {
    ///////////ERROR RESPONSE
    $msg = $msg == "" ? "Error" : $msg;
    return array("status" => 0, "msg" => $msg);
}

function success_res($msg) {
    ////////// SUCCESS RESPONSE
    $msg = $msg == "" ? "Success" : $msg;
    return array("status" => 1, "msg" => $msg);
}

function is_login() {
    /////////// CHECK PARAMETER USER_ID SET IN CODEIGNTER SESSION
    //////////// CODIGNATER SESSION IS NOT SESSTION OF PHP..CODEIGNATER USE COKIE FOR SESSTION
    $CI = & get_instance();
    $user_id = $CI->session->userdata('businessID');
    return $user_id;
}

function sub_businesses() {
    /////////// CHECK PARAMETER USER_ID SET IN CODEIGNTER SESSION
    //////////// CODIGNATER SESSION IS NOT SESSTION OF PHP..CODEIGNATER USE COKIE FOR SESSTION
    $CI = & get_instance();
    $sub_businesses = $CI->session->userdata('sub_businesses');
    return $sub_businesses;
}

function generateRandomString($length = 2) {
    ///////////GET RAMNDOM STRING FROM BELOW STRING
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function encrypt_string($string) {
    $key = "c91301c731a55b06f843e1bcebd31f22";
    $result = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) + ord($keychar));
        $result.=$char;
    }
    return base64_encode($result);
}

function decrypt_string($string) {
    $key = "c91301c731a55b06f843e1bcebd31f22";
    $result = '';
    $string = base64_decode($string);

    for ($i = 0; $i < strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) - ord($keychar));
        $result.=$char;
    }

    return $result;
}

function time_elapsed_string($ptime) {

//    $ptime = strtotime($ptime);
//    date_default_timezone_set('America/Los_Angeles');
//    $etime = time() - $ptime;

    $etime = $ptime;

    if ($etime < 1) {
        return '0 seconds';
    }

    $a = array(365 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60 => 'month',
        24 * 60 * 60 => 'day',
        60 * 60 => 'hour',
        60 => 'minute',
        1 => 'second'
    );
    $a_plural = array('year' => 'years',
        'month' => 'months',
        'day' => 'days',
        'hour' => 'hours',
        'minute' => 'minutes',
        'second' => 'seconds'
    );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}

function push_notification_ios($arg_device_token, $message_body) {
    $deviceToken = "" . $arg_device_token . "";


    $production = 1;
    if ($production) {
        $gateway = 'ssl://gateway.push.apple.com:2195';
    } else {
        $gateway = 'ssl://gateway.sandbox.push.apple.com:2195';
    }

// Create a Stream
    $ctx = stream_context_create();
// Define the certificate to use
    // stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck_prod2.pem');
// Passphrase to the certificate
    // stream_context_set_option($ctx, 'ssl', 'passphrase', 'tapinpush');

    stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
    stream_context_set_option($ctx, 'ssl', 'passphrase', 'id0ntknow');

// Open a connection to the APNS server
    $fp = stream_socket_client(
            $gateway, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

// Check that we've connected
    if (!$fp) {
        $error = "Failed to connect: $err $errstr" . PHP_EOL;
        return $error;
    }

    $body['aps'] = $message_body;
    // Encode the payload as JSON
    $payload = json_encode($body);
    // Build the binary notification
    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

    // Send it to the server
    $result = fwrite($fp, $msg, strlen($msg));
    fclose($fp);
    if (!$result) {
        //echo 'Error, notification not sent' . PHP_EOL;
        $return = error_res("Error, notification not sent");
        log_message('error', "****Failed to notify $deviceToken");
        return $return;
    } else {
        $return = success_res("Success, notification sent");
        log_message('info', "****Successfully notified $deviceToken");
        return $return;
    }
}

function staging_directory() {
    return 'tap-in';
}
