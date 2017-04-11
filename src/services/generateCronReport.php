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
require_once '../lib/model/ApiBilling.php';
require_once 'ExportReportExcel.php';
require_once 'ExcelSpout.php';

$logger = Logger::getRootLogger();
$service = new AppJsonService();
//SmsApiAdmin::filterAccess();
try {
    ini_set('max_execution_time', 7200);
    ini_set('memory_limit', '4024M');
    // Set variable from parameters
    // Get report data based clientid
    $apiReport = new ApiReport();
//    $apiBilling = new ApiBilling();
    $exportData = new ExportReportExcel();
    $exportDataSpout = new ExcelSpout();
//    $client = $apiReport->getProfileClient($clientId);
    $listClient = $apiReport->getBillingClient();
   
    foreach ($listClient as $client){
        
    $billedSMS = $client['BILLED_SMS'];
    $idClient = $client['CLIENT_ID'];
    $unknown = $client['UNKNOWN'];
    $pending = $client['PENDING'];
    $delivered = $client['DELIVERED'];
    $deliveredDesc = $client['DELIVERED_DESC'];
    $undelivered = $client['UNDELIVERED'];
    $totalSms = $client['TOTAL_SMS'];
    $totalCharge = $client['TOTAL_CHARGE'];
    $provider = $client['PROVIDER'];
    $providerDesc = $client['PROVIDER_DESC'];

    $arrBilledSMS = explode(";", $billedSMS);
    $errorCode = false;

    $finBilledSMS = 0;
    if (in_array('Y', $arrBilledSMS)) {
        $finBilledSMS += 1;
    }
    if (in_array('N', $arrBilledSMS)) {
        $finBilledSMS += 2;
    }
    if (in_array('E', $arrBilledSMS)) {
        $errorCode = true;
    }

    $listUser = $apiReport->getUser($idClient);
    
//    var_dump($idClient);
    foreach ($listUser as $user) {
        $userId = $user['USER_NAME'];
        $lsReport = $apiReport->getDataCronReport($userId);
        $exportDataSpout->getDataScheduled($userId, $lsReport);
        break;
    }
}



    exit();
} catch (Exception $e) {
    $logger->error("$e");
    SmsApiAdmin::returnError($e->getMessage());
}
