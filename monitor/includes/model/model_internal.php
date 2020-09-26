<?php
date_default_timezone_set('America/Los_Angeles');
$conn = nil;

//define('__ROOT__', dirname(dirname(dirname(__FILE__))));
//$includePath = __ROOT__ . "../includes/";
include_once (dirname(dirname(__FILE__)) . '/config_db.inc.php');
include_once (dirname(dirname(__FILE__)) . '/pt_consts.inc');
include_once (dirname(dirname(__FILE__)) . '/error_logging/pt_error.php');
include_once (dirname(dirname(__FILE__)) . '/model/model.php');

function pt_find_corp_name($corp_name) {
    $conn = getDBConnection();
    $corp_name = $conn->real_escape_string($corp_name);
    $query = "SELECT corp_name, corp_id, sponsor from corp where corp_name = '$corp_name';";

    return (getDBresult($query));
}

function pt_insert_corp_data($corp_name, $sponsor) {
//    $conn = getDBConnection();
//    $corp_name = $conn->real_escape_string($corp_name);
//
//    $insert_corp_query = "INSERT INTO corp (corp_name, sponsor, active) values ('$corp_name', $sponsor, '1')";
//    getDBresult($insert_corp_query);
//
//    $corp_id = getDBConnection()->user_id;
//    $insert_corp_closet_query =  "INSERT INTO corp_closet (corp_id, closet_id, location_id, spot_group, building_name, closet_name, location_description, schedule_id)
//        values ($corp_id, $closet_id, $location_id, $spot_group, $building_name, $building_name,
//        $location_description, $schedule_id)";
//
//    return (getDBresult($insert_corp_closet_query));
}

function pt_find_all_corp_closets_for_corp_name($corp_name) {
    $query = "SELECT
        a.active as is_closet_active,
        a.building_name,
        a.location_description,
        a.location_id,
        a.closet_id,
        b.sponsor,
        b.active as  is_corp_active,
        b.corp_name,
        c.schedule_1 as schedule,
        a.spot_group
      FROM
          corp_closet a
          join corp b on a.corp_id = b.corp_id
          join closet_schedule c on a.schedule_id = c.closet_schedule_id
      WHERE
        b.corp_name = '$corp_name' limit 5;";

    return (getDBresult($query));
}

function pt_find_all_corp_closets_for_spot_group($spot_group) {
    $query = "SELECT
        a.active as is_closet_active,
        a.building_name,
        a.location_description,
        a.location_id,
        a.closet_id,
        b.sponsor,
        b.active as  is_corp_active,
        b.corp_name,
        c.schedule_1 as schedule,
        a.spot_group
      FROM
          corp_closet a
          join corp b on a.corp_id = b.corp_id
          join closet_schedule c on a.schedule_id = c.closet_schedule_id
      WHERE
        a.spot_group = 'spot_group';";

    return (getDBresult($query));
}


$cmdCounter = 0;
$return_result = array();
do {
    switch ($cmdCounter) {
        case 0:
            $pos = stripos($cmd, "find_corp_name");
            if ($pos !== false) {
                $_corp_name = filter_input(INPUT_GET, 'corp_name');
                $return_result = pt_find_corp_name($_corp_name);
                print json_encode($return_result);
            }
            break;

        case 1:
            $pos = stripos($cmd, "find_all_corp_closets_for_corp_name");
            if ($pos !== false) {
                $_corp_name = filter_input(INPUT_GET, 'corp_name');
                $return_result = pt_find_all_corp_closets_for_corp_name($_corp_name);
                print json_encode($return_result);
            }
            break;

        case 2:
            $pos = stripos($cmd, "find_corp_closet_data_with_closet_id");
            if ($pos !== false) {
                $_closet_id = filter_input(INPUT_GET, 'closet_id');
                $return_result = pt_find_corp_closet_data_with_closet_id($_closet_id);
                print json_encode($return_result);
            }
            break;

        case 2:
            $pos = stripos($cmd, "find_corp_closet_data_with_spot_group");
            if ($pos !== false) {
                $_spot_group = filter_input(INPUT_GET, 'spot_group');
                $return_result = pt_find_corp_closet_data_with_spot_group($_spot_group);
                print json_encode($return_result);
            }
            break;

        case 3:
            $pos = stripos($cmd, "insert_corp");
            if ($pos !== false) {
                $_corp_name = filter_input(INPUT_GET, 'corp_name');
                $_sponsor = filter_input(INPUT_GET, 'sponsor');
                $return_result = pt_insert_corp($_corp_name, $_sponsor);
                print json_encode($return_result);
            }
            break;

        case 4:
            $pos = stripos($cmd, "insert_corp_data");
            if ($pos !== false) {
                $_corp_name = filter_input(INPUT_GET, 'corp_name');
                $_sponsor = filter_input(INPUT_GET, 'sponsor');
                $return_result = pt_insert_corp_data($_corp_name, $_sponsor);
                print json_encode($return_result);
            }
            break;

        default:
            # code...
            break;
    }
    $cmdCounter++;
}
while ($cmdCounter < 5);
?>
