<?php

require_once(__DIR__ . '/BaseReport.php');

class CorpDriver extends BaseReport
{
    use \koolreport\clients\Bootstrap;
    use \koolreport\clients\jQuery;

    use \koolreport\inputs\Bindable;
    use \koolreport\inputs\POSTBinding;

    protected function defaultParamValues()
    {
        $defaultEndDate = date("Y-m-d");
        $defaultStartDate = date("Y-m-d", strtotime("-1 months"));
        return array(
            "status"=>1,
            "dateRange"=>array(
                $defaultStartDate,
                $defaultEndDate
            )
        );
    }

    protected function bindParamsToInputs()
    {
        return array(
            "dateRange"=>"dateRange"
        );
    }

    function setup()
    {
        $this->src('Corp')
        ->query("select corp.corp_name as 'Farmers Market', b.`name` as 'Vendor'
                    , cp.nickname as 'Nick Name', cp.email1 as 'Email', o.order_id as 'Order ID'
					, o.no_items as 'No. of Items', corp.location_abbr as 'Location'
					, corp.delivery_time as 'Pickup Time'
					, o.note as 'Order Note', o.pd_instruction as 'Delivery Instruction' from `order` o
                    left join corp corp on corp.corp_id = o.order_corp_id
                    left join consumer_profile cp on cp.uid = o.consumer_id
                    left join business_customers b on o.business_id = b.businessID
                where  (o.status = 2 or o.`status` = 3 or o.status = 5)
                and  FIND_IN_SET (o.business_id, corp.merchant_ids)
                and  ( (CAST(o.date AS DATE)) <= :endDate and (CAST(o.date AS DATE)) >= :startDate )
                order by 'Farmers Market', delivery_time;")
            ->params(array(
                ":startDate"=>$this->params["dateRange"][0],
                ":endDate"=>$this->params["dateRange"][1]
            ))
        ->pipe($this->dataStore("corp_orders"));
    }

}