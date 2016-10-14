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
//    $startDate = $_GET["fromDate"];
//    $endDate = $_GET["endDate"];
    $clientId = $_GET['clientID'];
    $userId = $_GET['userID'];
    $userName = $_GET['userName'];
    $month = $_GET['month'];
    $year = $_GET['year'];
    $sms_dr = $_GET['sms_dr'];

    // Get report data based clientid
    $apiReport = new ApiReport();
    $exportData = new ExportReportExcel();
    $exportDataSpout = new ExcelSpout();
    $client = $apiReport->getProfileClient($clientId);

    $billedSMS = $client['BILLED_SMS'];
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

    $lsReport = $apiReport->getDataReport($userId, $finBilledSMS, $errorCode, $deliveredDesc, $startDate, $endDate);
//    $totalCount = $apiReport->messageCount();
    //calll ExportReportExcel, parameter /home/fathir/Documents/SMS_BILLING/copy of gratikatecc2016-01.xlsx
//    $exportData->downloadData($userName, $lsReport);
     $exportDataSpout->downloadDataSpout($userName, $lsReport);

    exit();
} catch (Exception $e) {
    $logger->error("$e");
    SmsApiAdmin::returnError($e->getMessage());
}
