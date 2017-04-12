<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Fathir Wafda & Abdul Khoir
 *
 */

require_once '../init.d/init.php';
require_once '../lib/model/ApiBilling.php';
require_once 'ExportReportExcel.php';

$logger = Logger::getRootLogger();
//SmsApiAdmin::filterAccess();
try{
    $apiBilling = new ApiBilling();
    $exportData = new ExportReportExcel();
    $listClient = $apiBilling->getProfileClient();
    
    foreach ($listClient as $client){
        $clientId = $client['CLIENT_ID'];
        $billedSMS = $client['BILLED_SMS'];
        $unknown = $client['UNKNOWN'];
        $pending = $client['PENDING'];
        $delivered = $client['DELIVERED'];
        $undelivered = $client['UNDELIVERED'];
        $totalSms = $client['TOTAL_SMS'];
	$deliveredDesc = $client['DELIVERED_DESC'];
        $totalCharge = $client['TOTAL_CHARGE'];
        $provider = $client['PROVIDER'];
        $providerDesc = $client['PROVIDER_DESC'];        
        
        $arrBilledSMS = explode(";", $billedSMS);
        $errorCode = false;
        
        $finBilledSMS = 0;
        if(in_array('Y',$arrBilledSMS)){
            $finBilledSMS += 1;
        }
        if(in_array('N',$arrBilledSMS)){
            $finBilledSMS += 2;
        }
        if(in_array('E',$arrBilledSMS)){
            $errorCode = true;
        }       
        
        $listUser = $apiBilling->getUser($clientId);
//        var_dump('number : '.$errorCode. ' ' . count($listUser) . ' '. $clientId);
//        echo $errorCode;
//        $mydate = new DateTime();
//            $dataWaktu = $mydate->format('Y-m-d H:i:s');
//            echo ' Data sebelum ' . $dataWaktu . ' ' .$userId .' ';
        foreach ($listUser as $user){
            //var_dump('number : '.$finBilledSMS. '');
            $userId = $user['USER_NAME'];
            $lsReport = $apiBilling->getDataReport($userId, $finBilledSMS, $errorCode, $deliveredDesc);
//            var_dump(count($lsReport));
            
            //calll ExportReportExcel, parameter --> user, lsReport;
            $exportData->exportData($userId, $lsReport);
////            var_dump("Finish generateReport for user" .datetime);
//            echo ' Data sesudah ' . $dataWaktu. ''. '-->' .$userId .''
//                    . '  ';
            break;
        }
        
    }
    exit();
   
} catch (Throwable $e) {
    $logger->error("$e");
//    SmsApiAdmin::returnError($e->getMessage());
}
