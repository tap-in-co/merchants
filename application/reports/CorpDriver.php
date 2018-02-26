<?php

require APPPATH."/libraries/koolreport/autoload.php";

class CorpDriver extends \koolreport\KoolReport
{
    use \koolreport\clients\Bootstrap;
    use \koolreport\clients\jQuery;

    function settings()
    {
        return array(
            "assets"=>array(
                "path"=>"../../assets",
                "url"=>base_url()."assets",
            ),
            "dataSources"=>array(
                "Corp"=>array(
                    "connectionString"=>"mysql:host=localhost;dbname=artdoost_local_tapin",
                    "username"=>"artdoost_dbadmin",
                    "password"=>"id0ntknow",
                    "charset"=>"utf8"
                )
            )
        );
    }
    function setup()
    {
        $this->src('Corp')
        ->query("select  b.`name` as Merchant, cp.nickname as 'Nick Name', o.order_id as 'Order ID', o.no_items as 'No. of Items'
                , o.note as 'Order Note', o.pd_instruction as 'Delivery Instruction' from `order` o
left join consumer_delivery cd on cd.consumer_id = o.consumer_id
left join consumer_profile cp on cp.uid = o.consumer_id
left join business_customers b on o.business_id = b.businessID
where  (o.status = 2 or o.`status` = 3 or o.status = 5)  
	and cd.delivery_address_name = \"OIT - Wisonville\" order by merchant;")
        ->pipe($this->dataStore("corp_orders"));
    }
}