<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Fathir Wafda 
 *
 */
require_once '../init.d/init.service.php';
require_once '../init.d/init.php';
require_once '../lib/model/ApiReport.php';
require_once 'ExportReportExcel.php';
require_once 'ExcelSpout.php';

$logger = Logger::getRootLogger();
$service = new AppJsonService();
//SmsApiAdmin::filterAccess();
try {
    ini_set('max_execution_time', 7200);
    ini_set('memory_limit', '4024M');
    // Set variable from parameters
    $clientId = $_GET['clientID'];
    $userId = $_GET['userID'];
    $userName = $_GET['userName'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $sms_dr = $_GET['sms_dr'];
    $check = $_GET['check'];

    // Get report data based clientid
    $apiReport = new ApiReport();
    $exportData = new ExportReportExcel();
    $exportDataSpout = new ExcelSpout();
    
    if(!empty($sms_dr)){
        $lastUpdated = $exportDataSpout->checkFile($userName, $month, $year);
        if(!empty($lastUpdated)){ 
            if($check != 'TRUE'){
                $lsReport = $apiReport->getDataReport($userId, $month, $year, $lastUpdated);
                $exportDataSpout->downloadDataSpout($userName, $month, $year, $lsReport);                
            }
            else{
                echo "Exist";
                exit();                
            }
        }else{
//            $return = array("error" => "Not exist");
//            echo json_encode($return);
            echo "File Doesn't Exist";
            exit();
        }
    } else {
        $exportDataSpout->getLastRecordDateTime($userName.'.xlsx', $month, $year);
    }

    exit();
} catch (Exception $e) {
    $logger->error("$e");
    SmsApiAdmin::returnError($e->getMessage());
}
