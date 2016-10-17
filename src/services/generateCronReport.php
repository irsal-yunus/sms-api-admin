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

function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


$logger = Logger::getRootLogger();
$logger->info("Start generating billing report");

try {
    $execute_time = microtime_float();
    
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
        
        $exportDataSpout = new ExcelSpout();
        $generateLastMonth = false;
        $lastMonth = date('m', strtotime('last month'));
        $lastYear = date('Y');
        if($lastMonth == '12') {
            $lastYear = date('Y', strtotime('last year'));
        } 

        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $lastYear . '-' . $lastMonth . '/';

        // check if there is report generated last month if now is in date 1st
        if (is_dir($directory) && date('d') == '01') {
            $generateLastMonth = true;
        }

        foreach ($listUser as $user) {
            $userId = $user['USER_NAME'];

            if($generateLastMonth) {
                $lastUpdated = $exportDataSpout->getLastDateFromReport($userId, $lastMonth, $lastYear);
                $lsReport = $apiReport->getDataCronReport($userId, $lastMonth, $lastYear, $lastUpdated);
                $exportDataSpout->getDataScheduled($userId, $lsReport);
            }

            $lastUpdated = $exportDataSpout->checkFile($userId, date('m'), date('Y'));
            $lastUpdated = $lastUpdated === false ? false : date("Y-m-d", strtotime("$lastUpdated +1 days"));

            $lsReport = $apiReport->getDataCronReport($userId, date('m'), date('Y'), $lastUpdated);
            //if(count($lsReport) != 0){
                //var_dump($lsReport);
                $exportDataSpout->getDataScheduled($userId, $lsReport);
            //}
            $memory[] = memory_get_peak_usage(1);
        }
    }
    
    $memory       = round((array_sum($memory) / count($memory))/1024/1024, 2); 
    $execute_time = microtime_float() - $execute_time;
    $execute_time = substr($execute_time,0,4);
    
    $logger->info("Finished generating billing report | Execution Time: ".$execute_time ."s | Memory: ".$memory."MB");
} catch (Throwable $e) {
    $logger->error($e->getMessage());
}
