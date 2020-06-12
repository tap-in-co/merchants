<?php
require APPPATH."/libraries/koolreport/core/autoload.php";
date_default_timezone_set('America/Los_Angeles');

class BaseReport extends \koolreport\KoolReport
{
    function settings()
    {
        $db_host     = getenv('DB_Host');
        $db_name     = getenv('DB_Name');
        $db_user     = getenv('DB_Username');
        $db_password = getenv('DB_Password');

        return array(
            "assets"=>array(
                "path"=>"../../assets",
                "url"=>base_url()."assets",
            ),
            "dataSources"=>array(
                "Corp"=>array(
                    "connectionString"=>"mysql:host=$db_host;dbname=$db_name",
                    "username"=>"$db_user",
                    "password"=>"$db_password",
                    "charset"=>"utf8_general_ci"
                )
            )
        );
    }
}
