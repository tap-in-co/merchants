<?php

$pt_model= dirname(dirname(__FILE__)) . '/model/pt_model.php';
include_once ($pt_model);

function get_argument($method, $optional, $field_name, $error_value="", $default_value ="")
{
    if ($method == "GET")
        $method = INPUT_GET;
    else if ($method == "POST")
        $method = INPUT_POST;

    $value = (string)filter_input($method, $field_name);
    if (  empty($value) && !$optional) {
        //error
        return '-1';
    } else if (empty($value)) {
        $value = $default_value;
    }

    if (!empty($error_value) && $value == $error_value)
        return '-1';

    return $value;
}

function contact_info_for_all_residential_customers() {
    $return_result  = model_contact_info_for_all_residential_customers();
    //$json_result = json_encode($return_result);

    return ($return_result);
}

function contact_info_for_all_not_active_residential_customers() {
    $return_result  = model_contact_info_for_not_active_customer(6);
    //$json_result = json_encode($return_result);

    return ($return_result);
}


function contact_info_for_residential_customer($home_phone) {
    $return_result  = model_contact_info_for_residential_customer($home_phone);

    return ($return_result);
}

function register_corp_customer_with_closet_id($closet_id)
{
    //$closet_id = get_argument("GET", false, 'closet_id', "-1", "0");

    $corp_id = get_argument("GET", false, 'corp_id', "-1","0");
    $employee_id = get_argument("GET", true, 'employee_id', "-1", "0");

    $first_name = get_argument("GET", false, 'first_name');
    $last_name = get_argument("GET", false, 'last_name');
    $address1 = get_argument("GET", true, 'address1');
    $address2 = get_argument("GET", true, 'address2');
    $zip_code = get_argument("GET", true, 'zip_code');
    $home_phone = get_argument("GET", true, 'phone_no');
    $email = get_argument("GET", false, 'email');
    $password = get_argument("GET", false, 'password');

    $cc_number = get_argument("GET", false, 'cc_number', "0", "0");
    $cc_expdate = get_argument("GET", false, 'cc_expdate', "0", "0");
    $cc_cvv = get_argument("GET", false, 'cc_cvv', "0", "0");

    $s_d_i = get_argument("GET", true, 's_d_i', "", "");
    $alldriver = get_argument("GET", true, 'alldriver', "", "");

    $best_way_contact_code = get_argument("GET", true, 'best_way_contact_code', "-1", "0");

    return insert_corp_customer($closet_id, $corp_id, $employee_id, $first_name, $last_name, $address1, $address2, $zip_code, $home_phone, $email, $password, $cc_number, $cc_expdate, $cc_cvv, $s_d_i, $alldriver, $best_way_contact_code);
}


// main block
$cmd = filter_input(INPUT_GET, 'cmd');
$return_result = array();

// process loop
$cmdCounter = 0;
do {
    if ($cmdCounter == 0) {
        $pos = stripos($cmd, "register_corp_customer_with_closet_id");
        if ($pos !== false) {
           $closet_id = get_argument("GET", false, 'closet_id', "-1", "0");
           if ($closet_id > 0) {
               $ret_code = register_corp_customer_with_closet_id($closet_id);
           } else  {
               $closet_name = get_argument("GET", false, 'closet_name', "", "");
               $company_name = get_argument("GET", false, 'closet_name', "", "");

               if (empty($closet_name) || empty($company_name) ) {
                   $ret_code = -1;
               }
               else {
                   $closet_id = get_closet_id($corp_name, $closet_name);
                   $ret_code = register_corp_customer_with_closet_id($closet_id);

               }

           }

            echo "$ret_code";
            break;
        }
    }
    if ($cmdCounter == 1) {
        $ret_code = -1;
        $pos = stripos($cmd, "delivery_for_closet_on_date");
        if ($pos !== false) {
           $closet_code = get_argument("GET", false, 'closet_code', "-1", "");
           $date = get_argument("GET", false, 'date', "-1", "");
           if (!empty($closet_code) && !empty($date) ) {
               $result = spot_invoices_delivered_in_closet_on_date($date, $closet_code);
           }

            //echo "$ret_code";
            echo json_encode($result);
            break;
        }
    }
    if ($cmdCounter == 2) {
        $ret_code = -1;
        $pos = stripos($cmd, "delivery_for_closet_on_date");
        if ($pos !== false) {
           $closet_code = get_argument("GET", false, 'closet_code', "-1", "");
           $date = get_argument("GET", false, 'date', "-1", "");
           if (!empty($closet_code) && !empty($date) ) {
               $result = spot_invoices_delivered_in_closet_on_date($date, $closet_code);
           }

            //echo "$ret_code";
            echo json_encode($result);
            break;
        }
    }

    $cmdCounter++;
} while ($cmdCounter < 3);
?>

