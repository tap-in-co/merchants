<?php
require APPPATH."/libraries/koolreport/core/autoload.php";

class BaseReport extends \koolreport\KoolReport
{
    function settings()
    {
        return array(
            "assets"=>array(
                "path"=>"../../assets",
                "url"=>base_url()."assets",
            ),
            "dataSources"=>array(
                "Corp"=>array(
                    "connectionString"=>"mysql:host=localhost;dbname=Local_test_aws_market",
                    "username"=>"dbadmin",
                    "password"=>"id0ntknow",
                    "charset"=>"utf8_general_ci"
                )
            )
        );
    }
}
