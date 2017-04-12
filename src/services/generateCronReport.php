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
require_once __DIR__.'/../init.d/init.service.php';
require_once __DIR__.'/../init.d/init.php';
require_once __DIR__.'/../lib/model/ApiReport.php';
require_once __DIR__.'/../lib/model/ApiBilling.php';
require_once __DIR__.'/ExportReportExcel.php';
require_once __DIR__.'/ExcelSpout.php';

function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


$logger = Logger::getRootLogger();
$logger->info("Start generating billing report");

try {
    $execute_time = microtime_float();
    $defaultMaxExec   = ini_get('max_execution_time');
    $defaultMemLimit  = ini_get('memory_limit');
    ini_set('max_execution_time', 7200);
    ini_set('memory_limit', '4024M');

    $apiReport = new ApiReport();
    $exportData = new ExportReportExcel();
    $exportDataSpout = new ExcelSpout();
    $listClient = $apiReport->getBillingClient();
    
    
    $memory = []; 
    foreach ($listClient as $client){
        $idClient = $client['CLIENT_ID'];
        $listUser = $apiReport->getUser($idClient);
        
        $exportDataSpout   = new ExcelSpout();
        $generateLastMonth = false;
        $initDate     = (int)date('d');
        $fistDay      = date('Y-m-01');
        $today        = date('Y-m-d', strtotime('now'));
//        $fistDay      = date('Y-m-01');
//        $today        = date('Y-m-d');
        $lastMonth    = date('m', strtotime("$fistDay -1 month"));
        $lastYear     = date('Y', strtotime("$fistDay -1 month"));
        $currentMonth = date('m');
        $currentYear  = date('Y');
        die(json_encode(compact('initDate','firstDay','today')));
        //if($lastMonth == '12') {
        //    $lastYear = date('Y', strtotime('last year'));
        //} 

        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $lastYear . '-' . $lastMonth . '/';

        // check if there is report generated last month if now is in date 1st
        if (is_dir($directory) && $initDate < 4) {
            $generateLastMonth = true;
        }
        //$generateLastMonth = true;

        foreach ($listUser as $user) {
            $userId = $user['USER_NAME'];
            //echo "$userId\n";
            if($generateLastMonth) {
                //$logger->info("LAST MONTH $lastYear-$lastMonth");
                $lastUpdated = $exportDataSpout->getLastDateFromReport($userId, $lastMonth, $lastYear);
                $lsReport = $apiReport->getDataCronReport($userId, $lastMonth, $lastYear, $lastUpdated, true);
                $exportDataSpout->getDataScheduled($userId, $lsReport, $lastMonth, $lastYear);
            }
            
            if($initDate > 3){
                //$lastUpdated = $exportDataSpout->checkFile($userId, date('m'), date('Y'));
                $lastUpdated = $exportDataSpout->checkFile($userId, $currentMonth, $currentYear);
                $lastUpdated = $lastUpdated !== false ? date("Y-m-d", strtotime("$lastUpdated +1 days")) : false;

                //$lsReport = $apiReport->getDataCronReport($userId, date('m'), date('Y'), $lastUpdated);
                $lsReport = $apiReport->getDataCronReport($userId, $currentMonth, $currentYear, $lastUpdated);
                //if(count($lsReport) != 0){
                    $exportDataSpout->getDataScheduled($userId, $lsReport);
                //}
//                else{
//                    $logger->info("$currentYear-$currentMonth Report for user $userId not generated, there is no data.");
//                }
                
            }
            $memory[] = memory_get_peak_usage(1);
        }
    }
    
    $memory       = round((array_sum($memory) / count($memory))/1024/1024, 2); 
    $execute_time = microtime_float() - $execute_time;
    $execute_time = round($execute_time,2);
    
    $logger->info("Finished generating billing report | Execution Time: ".$execute_time ."s | Memory: ".$memory."MB");

} catch (Throwable $e) {
    echo json_encode($e->getMessage(),192);
    $logger->error('generateCronReport Error: '.$e->getMessage());
}
