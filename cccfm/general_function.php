<?php


    function base_url($path)
    {
        $url=$_SERVER['APP_HOSTNAME'].$path;
        return $url;
    }

?>