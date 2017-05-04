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
    $execute_time     = microtime_float();
    $defaultMaxExec   = ini_get('max_execution_time');
    $defaultMemLimit  = ini_get('memory_limit');
    ini_set('max_execution_time', 7200);
    ini_set('memory_limit', '4024M');

    $apiReport       = new ApiReport();
    $exportData      = new ExportReportExcel();
    $exportDataSpout = new ExcelSpout();
    $listClient      = $apiReport->getBillingClient();
    
    
    $memory = []; 
    foreach ($listClient as $client){
        $idClient           = $client['CLIENT_ID'];
        $listUser           = $apiReport->getUser($idClient);
        $exportDataSpout    = new ExcelSpout();
        
        $initDate           = (int)date('d');
        $fistDay            = date('Y-m-01');
        $today              = date('Y-m-d');
        $lastMonth          = date('m', strtotime("$fistDay -1 month"));
        $lastYear           = date('Y', strtotime("$fistDay -1 month"));
        $currentMonth       = date('m');
        $currentYear        = date('Y');
        
        $directory          = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $lastYear . '-' . $lastMonth . '/';
        $generateLastMonth  = is_dir($directory) && $initDate < 4;

        
        foreach ($listUser as $user) {
            $userId = $user['USER_NAME'];
            //echo "$userId\n";
            if($generateLastMonth) {
                $lastUpdated = $exportDataSpout->getLastDateFromReport($userId, $lastMonth, $lastYear);
                $lsReport    = $apiReport->getDataCronReport($userId, $lastMonth, $lastYear, $lastUpdated, true);
                $exportDataSpout->getDataScheduled($userId, $lsReport, $lastMonth, $lastYear);
            }
            
            if($initDate > 3){
                $lastUpdated = $exportDataSpout->checkFile($userId, $currentMonth, $currentYear);
                $lastUpdated = $lastUpdated !== false ? date("Y-m-d", strtotime("$lastUpdated +1 days")) : false;

                $lsReport    = $apiReport->getDataCronReport($userId, $currentMonth, $currentYear, $lastUpdated);
                $exportDataSpout->getDataScheduled($userId, $lsReport);
                
            }
            $memory[] = memory_get_peak_usage(1);
        }
    }
   
    // Package all billing report on previous month
    if ($initDate < 4) {
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $lastYear . '-' . $lastMonth;
        $logger->info('Packaging all report '.$lastYear.'-'.$lastMonth.' period');
        echo exec('zip -j '.$directory.'/BillingReport_'.$lastYear.'-'.$lastMonth.'.zip '.$directory.'/*xlsx').PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
    }
   
    // Package all billing report new 
    $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $currentYear . '-' . $currentMonth;
    $logger->info('Packaging all report for '.$currentYear.'-'.$currentMonth.' period');
    echo exec('zip -j '.$directory.'/BillingReport_'.$currentYear.'-'.$currentMonth.'.zip '.$directory.'/*xlsx').PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;

    
    // calculating memory and time consuming for running this script
    $memory       = round((array_sum($memory) / count($memory))/1024/1024, 2); 
    $execute_time = microtime_float() - $execute_time;
    $execute_time = substr($execute_time,0,4);

    $logger->info("Finished generating billing report | Execution Time: ".$execute_time ."s | Memory: ".$memory."MB");
   
} catch (Throwable $e) {
    $logger->error('generateCronReport Error: '.$e->getMessage());
}
