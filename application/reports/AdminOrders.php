<?php
require_once(__DIR__ . '/BaseReport.php');

class AdminOrders extends BaseReport
{
    use \koolreport\inputs\Bindable;
    use \koolreport\inputs\POSTBinding;
    use \koolreport\export\Exportable;

    use \koolreport\clients\Bootstrap;
    use \koolreport\clients\jQuery;

    /* corp with the name of tapforall has all the business ids that use for all the corps*/
    function setup()
    {

    $this->src('Corp')
        ->query("SELECT
    order_id AS 'Order ID', sm.status_name as Status,
    b.`name` AS Merchant,
    c.nickname AS 'Name',
    c.email1 AS Email,
    o.cc_last_4_digits AS 'Last 4 Digits',
    o.subtotal AS 'Sub Total',
    o.reject_reason AS 'Reject Reason',
    o.payment_processor_message AS 'Payment Error',
    c.sms_no AS 'Phone'
FROM
    `order` o
    LEFT JOIN business_customers b ON b.businessID = o.business_id
    LEFT JOIN consumer_profile c ON c.uid = o.consumer_id
    left join order_status_map sm on sm.status = o.status

WHERE
    FIND_IN_SET (o.STATUS, :status)
    AND find_in_set (o.business_id, (SELECT merchant_ids FROM `corp` WHERE corp_id in (:corp_ids)))
    and  ( (CAST(o.date AS DATE)) < STR_TO_DATE(:endDate,'%Y-%m-%d') and (CAST(o.date AS DATE)) >= STR_TO_DATE(:startDate,'%Y-%m-%d') )
    ORDER BY
    Merchant,
    Email,
    Status,
    o.date DESC;
    ")
            ->params(array(
                ":status"=>$this->params["status"],
                ":startDate"=>$this->params["dateRange"][0],
                ":endDate"=>$this->params["dateRange"][1],
                ":corp_ids"=>$this->params["corp_ids"]
        ))
        ->pipe($this->dataStore("corp_orders"))
        ->requestDataSending();

      $this->src('Corp')
        ->query("Select status as status_id, status_name from order_status_map;")
        ->pipe($this->dataStore("status"));
    }

    protected function defaultParamValues()
    {
        return array(
            "status"=>"1,2",
            "corp_ids" =>$this->params["corp_ids"],
            "dateRange"=>array(
                $this->params["defaultStartDate"],
                $this->params["defaultEndDate"]
            )
        );
    }

    protected function bindParamsToInputs()
    {
        return array(
            "dateRange"=>"dateRange",
            "status"=>"status",
            "corp_ids"=>"corp_ids"
        );
    }
}
