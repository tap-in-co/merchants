<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH."/reports/AdminOrders.php";

class AdminOrderReport extends CI_Controller {

    public function __construct() {
        parent::__construct();
        ////////////DEFAULT LOAD BELOW FUNCTIONLITY WHEN CALL V1 CONTROLLER
        /////// LOAD LIBRARY VALIDATION CLASS
        $this->load->library('validation');

        $defaultStartDate = $this->session->corp_dates['order_start_date'];
        $defaultStartDate = date("Y-m-d", strtotime($defaultStartDate));
        $defaultEndDate = $this->session->corp_dates['order_end_date'];
        $defaultEndDate = date("Y-m-d", strtotime($defaultEndDate));

        $corp_ids = $this->session->corp_ids;
        $params = array("defaultStartDate"=>$defaultStartDate, "defaultEndDate"=>$defaultEndDate, "corp_ids"=>$corp_ids);
        $report = new AdminOrders($params);
        $report->run()->render();
        ///// LOAD MODEL CLASS
        $this->load->model('m_site');
        ////// RESONSE HEADER CONTEN TYPRE SET FROM DEFAULT(TEXT/HTML) TO APPLICATION/JSON
    }

    function index() {
        //// DEFAULT SITE CONTROLLER METHOD CALL
//        $data['business_list'] = $this->m_site->get_business_list();
//        $this->load->view('v_corpDriverReport');
    }
}
