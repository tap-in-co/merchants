<?php
// require_once "../../../load.koolreport.php";
require_once(__DIR__ . '/BaseReport.php');

// use \koolreport\querybuilder\DB;

class UnverifiedProducts extends BaseReport
{
    // use \koolreport\inputs\Bindable;
    // use \koolreport\inputs\POSTBinding;

    // use \koolreport\clients\Bootstrap;
    // use \koolreport\clients\jQuery;

    /* corp with the name of tapforall has all the business ids that use for all the corps*/
    protected function setup()
    {
        $this->src('Corp')
        ->query("SELECT
            order_id AS 'Order ID',
            b.`name` AS Merchant,
            c.nickname AS 'Nick Name',
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
            WHERE
            (o.STATUS = 2 or  o.STATUS = 3 or o.STATUS = 1)
            AND  FIND_IN_SET(o.business_id,(SELECT merchant_ids FROM `corp` WHERE corp_name = 'CCCFM Walnut Creek Market 2020'))
            and  ( (CAST(o.date AS DATE)) <= '2020-06-30' and (CAST(o.date AS DATE)) >= '2020-05-01' )
            ORDER BY
            o.date DESC
            ")
        ->pipe($this->dataStore("corp_orders"));
    }

}