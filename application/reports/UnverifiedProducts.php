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
        ->query("select b.name as 'Business Name', p.name as 'Product Name', short_description as 'Short Description', pg.category_name as 'Category'
, p.`timestamp` as 'Modification Time' from product  p
left join business_customers b on b.businessID = p.businessID
left join product_category pg on pg.`product_category_id` = p.product_category_id
where varified=0;
            ")
        ->pipe($this->dataStore("corp_orders"));
    }

}