<?php
// Turn off all error reporting
error_reporting(0);

date_default_timezone_set('America/Los_Angeles');
$conn = 0;
$conn_error = 0;

const WORK_STEP_ADD = 1;
const WORK_STEP_PULL = 2;
const WORK_STEP_END = 3;

//define('__ROOT__', dirname(dirname(dirname(__FILE__))));
//$includePath = __ROOT__ . "../includes/";
$includes_path = (dirname(dirname(__FILE__)) . '/config_db.inc.php');
include_once (dirname(dirname(__FILE__)) . '/config_db.inc.php');
//include_once (dirname(dirname(__FILE__)) . '/consts.inc');
// include_once (dirname(dirname(__FILE__)) . '/error_logging/error.php');


/*--------- database general purpose functions -----------------*/
function connectToDB() {
    global $db_host, $db_user, $db_pass, $db_name;
    $conn = mysqli_connect('p:' . $db_host, $db_user, $db_pass, $db_name) or die("Error " . mysqli_error($conn));
    $GLOBALS['conn'] = $conn;

    return $conn;
}

function getDBConnection() {
    $conn = $GLOBALS['conn'];
    if ($conn == 0){
        $conn = connectToDB();
    }
    $GLOBALS['conn'] = $conn;
    return ($conn);
}

function getDBresulRowsToArray($query) {
    $conn = connectToDB();
    $conn->set_charset("utf8");
    $result = $conn->query($query);

    $resultArr = array();
    while ($row = mysqli_fetch_row($result)) {
        $resultArr[] = $row[0];
    }
    return $resultArr;
}

function getDBresult($query)
{
    global $conn_error;

    $conn = connectToDB();
    $conn->set_charset("utf8");
    $result = $conn->query($query);

    //before fetching, fetching sets the error to 0
    $conn_error = $result;


    $resultArr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $resultArr[] = $row;
    }

    return $resultArr;
}


function insertOrUpdateQuery($query)
{
    $conn = connectToDB();

    if ($conn->query($query) === TRUE) {
        return ($conn->affected_rows);
    } else {
        return -1;
    }
}

function executeDBQuery($query) {
    $conn = connectToDB();

    if ($conn->query($query) === TRUE) {
        return 1;
    }
    else {
        return -1;
    }
}

/********************************************************************************************************/
/*   SIS Inv                                                                                            */
/********************************************************************************************************/
/*-------- utility functions -------------------*/
function moveToTopOfArray($element, $array, $key) {

    $value = $element[$key];
    $array = array_filter($array, function($x) use ($key, $value) { return $x[$key] != $value; });

    array_unshift($array,$element);

    return $array;
}


function assign_job_schedule_id_to_another_batch($job_product_id, $from_shipping_id, $to_shipping_id, $batches) {
    $error = 0;
    if (empty($from_shipping_id)) {
        $from_shipping_id = 0;
    }
    $end_workstep = WORK_STEP_END;
    $csv_batches = implode(',', $batches);
    $batch_finder_query = "select * from batch where job_product_id = $job_product_id
      and archived = 0 and shipping_job_id = $to_shipping_id and batch.workstep <> $end_workstep and batch_id not in ($csv_batches);";
    $result = getDBresult($batch_finder_query);
    if (count($result) > 0) {
        $batch_id = $result[0]['batch_id'];
        $update_query = "update batch set shipping_job_id =  $from_shipping_id where batch_id = $batch_id;";
        $rc = insertOrUpdateQuery($update_query);
        if ($rc < 0) {
            $error= -1;
        }

    } else {
        $error = -2;
    }

    return $error;
}

//function getPreviousWorkstep($workflow, $current_workstep) {
//    $wfArray = explode(',', $workflow);
//    $currentWorkstepIndex = array_search('$current_workstep', $wfArray);
//    if ($currentWorkstepIndex < 1) {
//        return -1;
//    }
//    else {
//        return $wfArray[$currentWorkstepIndex-1];
//    }
//}

function getNextWorkstep($workflow, $currentWorkstep) {
    $wfArray = explode(',', $workflow);
    $currentWorkstepIndex = array_search("$currentWorkstep", $wfArray);
    if ($currentWorkstepIndex > MAX_WORKSTEP) {
        return -1;
    }
    else {
        return $wfArray[$currentWorkstepIndex+1];
    }
}
function getEndWorkstepID($worflow) {
    $wfArray = explode(',', $worflow);
    return end($wfArray);
}

function getWorkstepNameFromDB($workstep_id) {
    $query = "select workstep_name from workstep_info where workstep_id = $workstep_id";
    $result = getDBresult($query);
    return ($result[0]["workstep_name"]);
}


function login($userName, $password) {
    $query = "select count(*) from user  where user_name = '$userName' and password = '$password';";
    return (getDBresult($query));
}
/********************************************************************************************************/
/* business logic functions                                                                             */
/********************************************************************************************************/
function sis_worksteps() {
    $query = "select workstep_name from workstep_info;";
    return (getDBresult($query));
}

function get_info_for_job_id($job_id) {
    $query ="SELECT
    j.is_active,
    c.customer_name,
    j.job_id,
    j.order_date,
    j.due_date,
    j.end_date,
    j.note,
    p.product_id,
    t.product_name,
    p.batch_size,
    p.total_items,
    p.price_per_item,
    (p.total_items * p.price_per_item) as total_value
    FROM
    job j,
    customer c,
    job_product p,
    product t
    WHERE
    j.customer_id = c.customer_id
    AND p.job_id = j.job_id
    AND p.product_id = t.product_id
    AND j.job_id = $job_id;";

    return (getDBresult($query));
}


function get_orders_for_like_consumer_nickname($consumer_nickname) {
    $query ="SELECT
   cp.corp_name,
	 o.date as order_date,
	 b.name as vendor_name,
   o.timestamp as modify_date,
   o.cc_last_4_digits,
   o.status as order_status,
   o.no_items,
	 o.note,
	 o.order_id,
	 c.nickname,
	 c.email1,
	 c.sms_no,
	 o.reject_reason,
	 o.payment_processor_message,
	 o.fulfillment_time,
	 o.delivery_charge_amount,
	 o.total,
	 cp.delivery_location as pickup_location,
	 o.points_dollar_amount

   FROM
   `order` o,
   consumer_profile c,
   business_customers b,
   corp cp
   
	 WHERE
   o.consumer_id = c.uid
   AND b.businessID = o.business_id
   AND o.order_corp_id = cp.corp_id
   And c.nickname like \"%$consumer_nickname%\";";

    return (getDBresult($query));
}

function getjobProductResultForJobProductID($job_product_id) {
    $query = "select * from job_product where job_product_id = $job_product_id";
    return (getDBresult($query));
}

function getjobProductResultForJobID($job_id) {
    $query = "select job_id, job_product_id, product_id, batch_size, total_items from job_product where job_id = $job_id";
    return (getDBresult($query));
}

function add_batches_for_productJob($job_product_result) {
    $number_of_batches = floor($job_product_result["total_items"] / $job_product_result["batch_size"]) + 1;
    $nItemsInLastBatch = intval($job_product_result["total_items"]) % intval($job_product_result["batch_size"]);
    if ($nItemsInLastBatch == 0) {
        $number_of_batches--;
        $nItemsInLastBatch = $job_product_result["batch_size"];
    }
    $job_product_id = $job_product_result["job_product_id"];
    $queryForExistingBatches = "select count(*) from batch where  job_product_id = $job_product_id;";
    $numberOfexistingBatchesResult = getDBresult($queryForExistingBatches);
    $numberOfExistingBatches = $numberOfexistingBatchesResult[0]["count(*)"];

    $batchIDs = array();
    $conn = connectToDB();
    $prepared_stmt = "INSERT INTO batch (job_product_id, batch_qty, start_time) VALUES (?,?,now())";
    for ($i = $numberOfExistingBatches; $i < $number_of_batches; $i++) {
        $batch_qty = $job_product_result["batch_size"];
        if  ($i == $number_of_batches-1) {
            $batch_qty = $nItemsInLastBatch;
        }
        $prepared_query = $conn->prepare($prepared_stmt);
        $prepared_query->bind_param('si', $job_product_result["job_product_id"], $batch_qty);
        $rc = $prepared_query->execute();
        $batchIDs[] =  mysqli_insert_id($conn);
    }
    $batchIDsWithInfo["batches"] = $batchIDs;
    $batchIDsWithInfo["TotalBatches"] = $number_of_batches;
    $batchIDsWithInfo["Number of batch IDs generated"] = $number_of_batches - $numberOfExistingBatches;
    $batchIDsWithInfo["job_product_id"] = $job_product_id;

    return $batchIDsWithInfo;
}

function get_info_for_order_id($order_id) {
    $query = "SELECT
	 distinct (o.order_id),
   o.`status` as order_status,
	 p.price,
	 o.points_dollar_amount,
	 p.`name` as product_name,
	 p.short_description,
	 ot.quantity,
	 ot.item_note
	 /*p.product_category_id*/
	 FROM
   `order` o
	 left join order_item ot on ot.order_id = o.order_id
	 left join product p on p.product_id = ot.product_id
	 WHERE
   o.order_id = $order_id;";

    return (getDBresult($query));
}

function searchByOrderDate($order_date) {
    $query ="SELECT
  j.is_active,
  c.customer_name,
  j.job_id,
  j.order_date,
  j.due_date,
  j.end_date,
  j.note,
  p.product_id,
  t.product_name,
  p.batch_size,
  p.total_items,
  p.price_per_item,
  (p.total_items * p.price_per_item) as total_value
  FROM
  job j,
  customer c,
  job_product p,
  product t
  WHERE
  j.customer_id = c.customer_id
  AND p.job_id = j.job_id
  AND p.product_id = t.product_id
  And j.order_date like \"%$order_date%\";";

    return (getDBresult($query));
}

function getWorkStepNames() {
    $query = "select workstep_name, workstep_id from workstep_info;";
    return (getDBresult($query));
    // return (getDBresulRowsToArray($query));
}

function get_inventory_locationsForJobProduct($job_product_id, $workstep = 0) {
    $job_product_query = "select i.*  FROM job_product jp
    left join inventory_location i on i.inventory_location_id = jp.inventory_location_id
    where job_product_id = $job_product_id";

    $location_job_product = getDBresult($job_product_query)[0];

    if ($workstep ==  WORK_STEP_PULL) {
        $query = "select DISTINCT b.inventory_location_id, i.* from batch b
          left join inventory_location i on i.inventory_location_id = b.inventory_location_id
          where job_product_id = $job_product_id and workstep = $workstep and b.archived = 0 and i.archived = 0
          and ((b.inventory_location_id is not NULL) or (b.inventory_location_id > 0));";
    } else {
        $query = "SELECT * FROM inventory_location where archived = 0;";
    }

    $location_all  = getDBresult($query);

    return (moveToTopOfArray($location_job_product, $location_all, "inventory_location_id"));
}


//function get_inventory_locations() {
//    $query = "select * FROM inventory_location;";
//    return (getDBresult($query));
//}

//function get_inventory_locationsForCustomer($customer_id) {
//    if ($customer_id != 0)
//        $query = "select * FROM inventory_location where customer_id = $customer_id;";
//    else
//        $query = "select * FROM inventory_location;";
//
//    return (getDBresult($query));
//}

function get_customers() {
    $query = "select customer_name, customer_id FROM customer;";
    return (getDBresult($query));
}

function getNextWorkstepForBatch($batch_id) {
    $batchQuery = "select batch_id, workstep, workstep_name, p.product_id, p.workflow_id, worksteps from batch b
  left join job_product jb on jb.job_product_id = b.job_product_id
  left join product p on p.product_id = jb.product_id
  left join workflow w on w.workflow_id = p.workflow_id
  left join workstep_info ws on workstep = ws.workstep_id
  where batch_id = $batch_id";
    $batchInfo = getDBresult($batchQuery);
    $batchInfo[0]["next_workstep_id"] =  getNextWorkstep($batchInfo[0]["worksteps"], $batchInfo[0]["workstep"]);
    $next_workstep_id = $batchInfo[0]["next_workstep_id"];
    $next_workstep_query = "select * from workstep_info  where workstep_id = $next_workstep_id";
    $next_workstep_info = getDBresult($next_workstep_query);
    $batchInfo[0]["next_workstep_name"] =  $next_workstep_info[0]["workstep_name"];

    return ($batchInfo[0]);
}

function validate_batches($request) {
    $workstep_id = $request["workstep_id"];
    $job_product_id = $request["job_product_id"];
    $inventory_location_id = $request["inventory_location_id"];
    $given_job_id = $request["job_id"];

    if ($workstep_id == WORK_STEP_ADD) {
        $query = "select count(*) from batch where job_product_id= $job_product_id and workstep = $workstep_id;";
        $count_result = getDBresult($query);
        $nBatches = $count_result[0]['count(*)'];
    }
    if ($workstep_id == WORK_STEP_PULL) {
        $count1_query = "select count(*) from batch where job_product_id= $job_product_id and workstep = $workstep_id;";
        $count2_query = "select no_batches from job_shipping_schedule
          where job_shipping_schedule_id = $given_job_id and job_product_id= $job_product_id;";
        $work_step_end = WORK_STEP_END;
        $processed_batch_query = "select count(*) from batch where shipping_job_id = $given_job_id
          and workstep = $work_step_end and job_product_id = $job_product_id;";
        $count1_result = getDBresult($count1_query);
        $count2_result = getDBresult($count2_query);
        $processed_batches_count = getDBresult($processed_batch_query);
        $nBatches = min($count2_result[0]['no_batches'] - $processed_batches_count[0]['count(*)'], $count1_result[0]['count(*)']);
    }

    $end_workstep = WORK_STEP_END;
    $query = "select b.*, w.workstep_name from batch b, workstep_info w
      where b.workstep = w.workstep_id and b.workstep != $end_workstep and b.archived = 0;";

    $all_batches = getDBresult($query);
    $result_batches = [];
    $batchQTY = 0;
    foreach ($request["data"] as $batch_id) {
        $found = false;
        $result_batch = array();
        reset($all_batches);
        while ($row = current($all_batches)) {
            $batchQTY = 0;
            $error_code = 0;
            $error_message = "";
            if ($batch_id == $row["batch_id"]) {
                $found = true;

                $batchQTY = $row["batch_Qty"];

                if (strcmp($workstep_id, $row["workstep"]) !== 0) {
                    $row_workstep = $row["workstep_name"];
                    $error_message = "Wrong workstep($row_workstep)";
                    $error_code = "-1";
                }
                else if (strcmp($job_product_id, $row["job_product_id"]) !== 0) {
                    $row_job_product_id = $row["job_product_id"];
                    $error_message = "not correct product($row_job_product_id)";
                    $error_code = "-2";
                }
                else if ($workstep_id == 2) {
                    if (intval($inventory_location_id) != intval($row["inventory_location_id"])) {
                        $inventory_location_id = $row["inventory_location_id"];
                        $error_message = "not belong to location($inventory_location_id)";
                        $error_code = "-3";
                    }

                    // job schedule id (pull job id) stuff, remember that up to this point they are reserved for a  pull
                    else if ($given_job_id != $row["shipping_job_id"]) {
                        $to_shipping_id = $given_job_id;
                        $from_shipping_id = $row["shipping_job_id"];
                        if (assign_job_schedule_id_to_another_batch($job_product_id, $from_shipping_id, $to_shipping_id, $request["data"] ) < 0) {
                            $error_message = "Could not process Pull Id";
                            $error_code = "-4";
                        }
                        else {
                            $update_query = "update batch set shipping_job_id = $to_shipping_id where batch_id = $batch_id;";
                            if (insertOrUpdateQuery($update_query) < 0 ) {
                                $error_message = "Could not process Pull Id";
                                $error_code = "-4";
                            }
                        }
                    } else {
                        // everything is good confirm the reservation
//                        $update_query = "update batch set shipping_id_confirmed = 1 where batch_id = $batch_id;";
//                        insertOrUpdateQuery($update_query);
                    }

                }

                break;
            }
            next($all_batches);
        }
        if (!$found) {
            $error_message = "not in system";
            $error_code = "-10";
        }
        $result_batch["error_message"] = $error_message;
        $result_batch["error_code"] = $error_code;
        $result_batch["batch_id"] = $batch_id;
        $result_batch["batch_QTY"] = $batchQTY;

        $result_batches[] = $result_batch;
    }

    $final_result["message"] = "";
    $final_result["status"] = 0;
    $final_result["nBatches"] = $nBatches;
    $final_result["data"] = $result_batches;

//    $unset_confirmation_query = "update batch set shipping_id_confirmed = 0 where shipping_id_confirmed =1;";
//    insertOrUpdateQuery($unset_confirmation_query);

    return $final_result;
}

function process_batches($request) {
//    if (empty($request["customer_id"])) {
//        $customer_id = 0;
//    } else {
//        $customer_id = $request["customer_id"];
//    }
    if (empty($request["location_id"]) || $request["location_id"] < 0) {
        $inventory_location_id =0;
    } else {
        $inventory_location_id = $request["location_id"];
    }
    if (empty($request["shipping_address_id"]) || $request["shipping_address_id"] < 0) {
        $shipping_address_id = 0;
    } else {
        $shipping_address_id = $request["shipping_address_id"];
    }
    $given_job_id = $request["job_id"];
    $job_product_id = $request["job_product_id"];
    $workstep_id = $request["workstep_id"];
    $workstep_name = $request["workstep_name"];
    if (empty($workstep_name))
        $workstep_name = getWorkstepNameFromDB($workstep_id);

    $next_workstep_fieldName = $workstep_name . "_time";
    // $currentWorkstep = getPreviousWorkstep($all_batches[0]["worksteps"], $all_batches[0]["workstep"]);
    $conn = connectToDB();

    if ($inventory_location_id > 0) {
        $prepared_stmt = "Update batch set workstep = ?, $next_workstep_fieldName = now(),error_id = 0,
            inventory_location_id = $inventory_location_id where batch_id = ?";
    }
    elseif ( $shipping_address_id > 0 ) {
        $prepared_stmt = "Update batch set workstep = ?, $next_workstep_fieldName = now(),error_id = 0,
            shipping_address_id = $shipping_address_id where batch_id = ?";
    }
    else {
        $prepared_stmt = "Update batch set workstep = ?, $next_workstep_fieldName = now(), error_id = 0
            where batch_id = ?";
    }

    $prepared_error_stmt = "Update batch set error_id = ?, error_message = ?
      where batch_id = ?";

    foreach ($request["data"] as $batchData) {
        $batch = $batchData["batch"];
        $error_id = $batch["error_code"];
        $errorMessage = $batch["error_message"];
        $batch_id = $batch["batch_id"];
        $query = "select batch_id, b.shipping_job_id, b.`job_product_id`, j.customer_id, b.workstep, workstep_name, p.product_id,
      p.workflow_id, worksteps, jb.inventory_location_id, jb.shipping_address_id from batch b
      left join job_product jb on jb.job_product_id = b.job_product_id
      left join job j on jb.job_id = j.job_id
      left join product p on p.product_id = jb.product_id
      left join workflow w on w.workflow_id = p.workflow_id
      left join workstep_info ws on b.workstep = ws.workstep_id where batch_id = $batch_id;";

        $batchInfo = getDBresult($query);
        if (!$batchInfo) {
            continue;
        }

        if ($batchInfo[0]["workstep"] == 2) {
            $batch_shipping_job_id = $batchInfo[0]["shipping_job_id"];
            $shipment_query = "select * from job_shipping_schedule
            where job_shipping_schedule_id = $batch_shipping_job_id
            and job_shipping_schedule_id = $given_job_id";

            $shipment_info = getDBresult($shipment_query);
            if (count($shipment_info) == 0) {
                $conn->prepare($prepared_error_stmt);
                $error_id = -5;
                $errorMessage = "Wrong or nonexistence Pull Job ID";
            }
        }

        $rc = false;
        $nextWorkstepid = getNextWorkstep($batchInfo[0]["worksteps"], $batchInfo[0]["workstep"]);
        // $nextWorkstepName =  getWorkstepNameFromDB($nextWorkstepid);
        // if (strcmp($nextWorkstepName, $workstep_name) === 0) {
        if (!$nextWorkstepid) {
            $prepared_error_query = $conn->prepare($prepared_error_stmt);
            $error_id = -1;
            $errorMessage = "Cannot move from workstep";
            $prepared_error_query->bind_param('isi', $error_id, $errorMessage, $batch_id);
            $rc = $prepared_error_query->execute();
        }
//      else if (($nextWorkstepid == getEndWorkstepID($batchInfo["worksteps"])) &&
//          ($shipping_address_id > 0) && validatedShippingAddressID($shipping_address_id, $batchInfo[0]["customer_id"])
//      ) {
//          $prepared_error_query = $conn->prepare($prepared_error_stmt);
//          $error_id = -2;
//          $errorMessage = "Wrong shipping address is given!";
//          $rc = $prepared_error_query->bind_param('isi', $error_id, $errorMessage, $batch_id);
//          $rc = $prepared_error_query->execute();
//      }
        // no need to validate the inventory location, because location is given based on the customer id, and we are checking
        // customer_id already
        // else if ( strcmp($workstep_name, "inventory") === 0) &&
        //           ($inventory_location_id > 0) && $inventory_location_id != ($batchInfo[0]["inventory_location_id"]) ) {
        //   $prepared_error_query = $conn->prepare($prepared_error_stmt);
        //   $error_id = -3;
        //   $rc = $prepared_error_query->bind_param('isi',$error_id , "Wrong inventory location!", $batch_id);
        //   $rc = $prepared_error_query->execute();
        // }
        else if ($job_product_id  != $batchInfo[0]["job_product_id"])  {
            $prepared_error_query = $conn->prepare($prepared_error_stmt);
            $error_id = -4;
            $errorMessage = "Wrong product";
            $prepared_error_query->bind_param('isi', $error_id, $errorMessage, $batch_id);
            $rc = $prepared_error_query->execute();
        }
        else if ($error_id != 0)  {
            $prepared_error_query = $conn->prepare($prepared_error_stmt);
            $prepared_error_query->bind_param('isi', $error_id, $errorMessage, $batch_id);
            $rc = $prepared_error_query->execute();
        }
        else {
            //everything is good: given work-step is correctly the next work-step for this batch
            $prepared_query = $conn->prepare($prepared_stmt);
            $prepared_query->bind_param('ii', $nextWorkstepid, $batch_id);
            $rc = $prepared_query->execute();
        }
    }

    return $rc;
}

function get_no_processed_batches($duration) {
    $query = "select count(*) from batch WHERE timestamp BETWEEN $duration;";
    $count_result = getDBresult($query);

    return ($count_result[0]['count(*)']);
}

function get_no_processed_error_batches($duration) {
    $query = "select count(*) from batch WHERE timestamp BETWEEN $duration
  and error_id != 0;";
    $count_result = getDBresult($query);

    return ($count_result[0]['count(*)']);
}

function pullFromInventory($workstep) {
    if ($workstep == WORK_STEP_PULL)
        return true;
    else
        return false;
}


function get_job_ids_for_workstep($workstep) {
    if (pullFromInventory($workstep)) {
//        $query ="select DISTINCT b.shipping_job_id, j.job_id, c.customer_name, b.`batch_id`, b.workstep, jp.job_product_id, p.product_name, jp.batch_size,
//        jp.total_items, i.inventory_location_id, i.`name` as inventory_location, 'NA' as address
//        from batch b
//        left join job_product jp on b.job_product_id = jp.job_product_id
//        left join product p on jp.product_id = p.product_id
//        left join job j on j.job_id = jp.job_id
//        left join inventory_location i on b.inventory_location_id = i.inventory_location_id
//        left join customer c on j.customer_id = c.customer_id
//        join job_shipping_schedule sj on sj.job_id = jp.job_id
//        join job_shipping_schedule sp on sp.job_product_id = jp.job_product_id
//        where b.workstep = $workstep and b.shipping_job_id >= 0
//          and b.archived = 0 and jp.archived = 0 and p.archived = 0 and j.archived = 0
//          and sj.archived = 0 and i.archived = 0;";


        $query ="select distinct (js.`job_shipping_schedule_id`) as shipping_job_id, j.job_id, c.customer_name, b.`batch_id`, b.workstep, jp.job_product_id, p.product_name, jp.batch_size,
        jp.total_items, i.inventory_location_id, i.`name` as inventory_location, 'NA' as address
        from batch b
        left join job_shipping_schedule js on b.shipping_job_id = js.job_shipping_schedule_id
        left join job_product jp on b.job_product_id = jp.job_product_id
        left join product p on jp.product_id = p.product_id
        left join job j on j.job_id = jp.job_id
        left join inventory_location i on b.inventory_location_id = i.inventory_location_id
        left join customer c on j.customer_id = c.customer_id
        where b.workstep = $workstep
          and b.archived = 0 and jp.archived = 0 and p.archived = 0 and j.archived = 0
          and js.archived = 0 and i.archived = 0;";

    } else {
        $query ="select DISTINCT j.job_id, c.customer_name, b.`batch_id`, b.workstep, jp.job_product_id, p.product_name, jp.batch_size,
    jp.total_items, i.inventory_location_id, i.name as inventory_location, 'NA' as address
    from batch b left join job_product jp on b.job_product_id = jp.job_product_id
    left join product p on jp.product_id = p.product_id
    left join job j on j.job_id = jp.job_id
    left join inventory_location i on b.inventory_location_id = i.inventory_location_id
    left join customer c on j.customer_id = c.customer_id
    where b.workstep = $workstep
    and b.archived = 0 and jp.archived = 0 and p.archived = 0 and j.archived = 0
          and i.archived = 0 order by j.job_id ASC";
    }

    return (getDBresult($query));
}

function update_batch_workstep_inventory($workstep, $location_name, $batch_id) {
    $query ="select * from workstep_info where workstep_name = $workstep;";
    $workstepInfo = getDBresult($query);
    $workstep_id = $workstepInfo[0]["workstep_id"];

    $query ="select * from inventory_location where name = $location_name;";
    $result = getDBresult($query);
    $inventory_location_id = $result[0]["inventory_location_id"];

    $batchUpdate = "update batch set workstep = $workstep_id, inventory_location_id = $inventory_location_id where batch_id = $batch_id;";

    return (getDBresult($batchUpdate));
}


function delete_all_archive() {
    try {
        global $conn_error;

        $delete_statement = "delete from customer where archived = 1";
        getDBresult($delete_statement);
        if (!$conn_error) {
            throw new Exception("deleting archived customers did not work");
        }

        $delete_statement = "delete from product where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("deleting archived products did not work");
        }

        $delete_statement = "delete from inventory_location where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("deleting archived inventory_locations did not work");
        }

        $delete_statement = "delete from job where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("deleting archived jobs did not work");
        }

        $delete_statement = "delete from job_product where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("deleting archived job_products did not work");
        }

        $delete_statement = "delete from job_shipping_schedule where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("deleting archived job_shipping_schedules did not work");
        }

        $delete_statement = "delete from batch where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("deleting archived batches did not work");
        }

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        return -1;
    }

    return 0;
}

function unarchive_all() {
    try {
        global $conn_error;

        $delete_statement = "update customer set archived = 0 where archived = 1";
        getDBresult($delete_statement);
        if (!$conn_error) {
            throw new Exception("Un-archiving customers did not work");
        }

        $delete_statement = "update product set archived = 0 where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("Un-archiving products did not work");
        }

        $delete_statement = "update inventory_location set archived = 0 where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("Un-archiving inventory_locations did not work");
        }

        $delete_statement = "update job set archived = 0 where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("Un-archiving jobs did not work");
        }

        $delete_statement = "update job_product set archived = 0 where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("Un-archiving job_products did not work");
        }

        $delete_statement = "update job_shipping_schedule set archived = 0 where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("Un-archiving job_shipping_schedules did not work");
        }

        $delete_statement = "update batch set archived = 0 where archived = 1";
        getDBresult($delete_statement);
        if ($conn_error == false) {
            throw new Exception("Un-archiving batches did not work");
        }

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        return -1;
    }

    return 0;
}

// main block
$cmd = $_REQUEST['cmd'];
$return_result = array();
header('Content-type: application/json');

// process loop
$cmdCounter = 0;
$return_result = array();
do {
    /********************************************************************************************************/
    /*   SIS Inv                                                                                            */
    /********************************************************************************************************/
    if ($cmdCounter == 0) {
        $count_result = 0;
        $pos = stripos($cmd, "login");

        if ($pos !== false) {
            if (isset($_GET["user_name"])  && isset($_GET["password"]) ) {
                $user_name = $_GET["user_name"];
                $password = $_GET["password"];
                $count_result = login( $user_name, $password);
                $count_result = $count_result[0]['count(*)'];
            }

            echo json_encode($count_result);
            break;
        }
    }
    else if ($cmdCounter == 1) {
        $return_result = 0;
        $pos = stripos($cmd, "ListOfWorksteps");
        if ($pos !== false) {
            $return_result = sis_worksteps();
            echo json_encode($return_result);
            break;
        }
    }
    else if ($cmdCounter == 2) {
        $return_result = 0;
        // $pos = stripos($cmd, "AllBatchesForJob");
        $pos = stripos($cmd, "info_for_job_id");
        if ($pos !== false) {
            if (isset($_GET["job_id"])) {
                $job_id = $_GET["job_id"];
                $return_result = get_info_for_job_id($job_id);
                echo json_encode($return_result);
            }
            break;
        }
    }
    else if ($cmdCounter == 3) {
        $return_result = 0;
        // called from the admin web page
        $pos = stripos($cmd, "info_for_order_id");
        if ($pos !== false) {
            if (isset($_GET["order_id"])) {
                $order_id = $_GET["order_id"];
                $return_result = get_info_for_order_id($order_id);

                echo json_encode($return_result);
            }
            break;
        }
    }
    else if ($cmdCounter == 4) {
        $return_result = 0;
        // $pos = stripos($cmd, "AllBatchesForJob");
        $pos = stripos($cmd, "getWorkStepNames");
        if ($pos !== false) {
            $return_result = getWorkStepNames();
            header('Content-Type: application/json');
            echo json_encode($return_result);
            break;
        }
    }
    else if ($cmdCounter == 5) {
        $return_result = 0;
        // $pos = stripos($cmd, "AllBatchesForJob");
        $pos = stripos($cmd, "like_consumer_nickname");
        if ($pos !== false) {
            if (isset($_GET["customer_name"])) {
                $customer_name = $_GET["customer_name"];
                $return_result = get_orders_for_like_consumer_nickname($customer_name);
                header('Content-Type: application/json');
                echo json_encode($return_result);
            }
            break;
        }
    }
    else if ($cmdCounter == 6) {
        $return_result = 0;
        // $pos = stripos($cmd, "AllBatchesForJob");
        $pos = stripos($cmd, "searchByOrderDate");
        if ($pos !== false) {
            if (isset($_GET["order_date"])) {
                $order_date = $_GET["order_date"];
                $return_result = searchByOrderDate($order_date);
                header('Content-Type: application/json');
                echo json_encode($return_result);

            }
            break;
        }
    }
    else if ($cmdCounter == 7) {
        $return_result = 0;
        $pos = stripos($cmd, "generated_batch_ids_for_job_product_id");
        if ($pos !== false) {
            if (isset($_GET["job_product_id"])) {
                $job_product_id = $_GET["job_product_id"];
                $job_product_result = getjobProductResultForJobProductID($job_product_id);
                $return_result = add_batches_for_productJob($job_product_result[0]);

                header('Content-Type: application/json');
                echo json_encode($return_result);
            }
            break;
        }
    }
    else if ($cmdCounter == 8) {
        $pos = stripos($cmd, "generated_batch_ids_for_job_id");
        if ($pos !== false) {
            $return_result = 0;
            if (isset($_GET["job_id"])) {
                $job_id = $_GET["job_id"];
                $job_product_for_job_id_result = getjobProductResultForJobID($job_id);
                $final_result = [];
                foreach ($job_product_for_job_id_result as $job_product_row) {
                    $return_result = add_batches_for_productJob($job_product_row);
                    $final_result[] = $return_result;
                }
                header('Content-Type: application/json');
                echo json_encode($final_result);
            }
            break;
        }
    }
// else if ($cmdCounter == 8) {
//   $return_result = 0;
//   $pos = stripos($cmd, "batches_with_job_id");
//   if ($pos !== false) {
//     if (isset($_GET["job_id"]))
//       $job_id = $_GET["job_id"];
//     $job_product_result = getjobproductResultForJobID($job_id);
//     $return_result = add_batches_for_productJob($job_product_result[0]);

//     header('Content-Type: application/json');
//     echo json_encode($return_result);
//     break;
//   }
// }
    else if ($cmdCounter == 9) {
        $return_result = 0;
        $pos = stripos($cmd, "next_workstep_for_batch");
        if ($pos !== false) {
            if (isset($_GET["batch_id"])) {
                $batch_id = $_GET["batch_id"];
                $nextWorkStep = getNextWorkstepForBatch($batch_id);

                header('Content-Type: application/json');
                echo json_encode($nextWorkStep);
            }
            break;
        }
    }
    else if ($cmdCounter == 10) {
        $return_result = 0;
        $pos = stripos($cmd, "get_inventory_locations");
        if ($pos !== false) {
            if (isset($_GET["job_product_id"])) {
                $job_product_id = $_GET["job_product_id"];
                $workstep = $_GET["workstep"];
                $locations = get_inventory_locationsForJobProduct($job_product_id, $workstep);

                header('Content-Type: application/json');
                echo json_encode($locations);
            }

            break;
        }
    }
    else if ($cmdCounter == 11) {
        /////
        $request = json_decode(file_get_contents('php://input'), TRUE);
        $cmd_post = $request["cmd"];
        $pos = stripos($cmd_post, "validate_batches");
        if ($pos !== false) {
            echo json_encode(validate_batches($request));

            break;
        }
    }
    else if ($cmdCounter == 12) {
        $request = json_decode(file_get_contents('php://input'), TRUE);
        $cmd_post = $request["cmd"];
        $pos = stripos($cmd_post, "process_batches");
        if ($pos !== false) {
            $process_status = process_batches($request);
            $final_result["message"] = "";
            $final_result["status"] = $process_status;
            echo json_encode($final_result);

            break;
        }

    }
    else if ($cmdCounter == 13) {
        $pos = stripos($cmd, "customer_support_utility_dashboard");
        if ($pos !== false) {
            $duration = " NOW() - INTERVAL 1 DAY AND NOW()";

            $count_today_processed_batches = get_no_processed_batches($duration);
            $count_today_error_batches = get_no_processed_error_batches($duration);

            $result = array();
            // $pos = str
            $result[0]['name'] = 'New orders today';
            $result[0]['value'] = $count_today_processed_batches;
            $result[1]['name'] = 'Processed orders today';
            $result[1]['value'] = $count_today_error_batches;

            $json = json_encode($result);
            echo "$json";

            break;
        }

    }
    else if ($cmdCounter == 14) {
        $pos = stripos($cmd, "get_customers");
        if ($pos !== false) {
            $customerInfo = get_customers();
            header('Content-Type: application/json');
            echo json_encode($customerInfo);
            break;
        }
    }
    else if ($cmdCounter == 15) {
        $pos = stripos($cmd, "get_job_ids_for_workstep");
        if ($pos !== false) {
            $workstep = $_GET["workstep"];
            $return_result =[];
            $job_ids = get_job_ids_for_workstep($workstep);
            $return_result["data"] = $job_ids;
            echo json_encode($return_result);
            break;
        }
    }
    else if ($cmdCounter == 16) {
        $pos = stripos($cmd, "update_batch_workstep_inventory");
        if ($pos !== false) {
            $workstep = $_GET["workstep"];
            $batch_id = $_GET["batch_id"];
            $inventory_location_name  = $_GET["inventory_location_name"];
            $return_result =[];
            $result = update_batch_workstep_inventory($workstep, $inventory_location_name, $batch_id);
            $return_result["message"] = "";
            $return_result["status"] = $result;
            echo json_encode($return_result);
            break;
        }
    }
    else if ($cmdCounter == 17) {
        $return_result = 0;
        $pos = stripos($cmd, "delete_all_archive");
        if ($pos !== false) {
            $return_result = delete_all_archive();
            header('Content-Type: application/json');
            echo json_encode($return_result);
            break;
        }
    }
    else if ($cmdCounter == 18) {
        $return_result = 0;
        $pos = stripos($cmd, "unarchive_all");
        if ($pos !== false) {
            $return_result = unarchive_all();
            header('Content-Type: application/json');
            echo json_encode($return_result);
            break;
        }
    }

    $cmdCounter++;

} while ($cmdCounter < 19);

