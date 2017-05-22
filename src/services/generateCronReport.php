#!/usr/bin/php -tt
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Fathir Wafda
 * @author Basri Yasin 
 */
require_once __DIR__.'/../configs/config.php';
require_once __DIR__.'/../init.d/init.service.php';
require_once __DIR__.'/../init.d/init.php';
require_once __DIR__.'/../lib/model/ApiReport.php';
require_once __DIR__.'/../lib/model/ApiBilling.php';
// require_once __DIR__.'/ExportReportExcel.php';
// require_once __DIR__.'/ExcelSpout.php';

echo exec('clear');
$memory_limit = ini_set('memory_limit', '4024M');

$logger = Logger::getRootLogger();
$logger->info("Start generating billing report");

// ---------------------------------------------------
//                D E B U G   O N L Y 
// ---------------------------------------------------
    function savePeformaceHistory($s) {
        $f = fopen('new_billing_peformance.history', 'a');
        fwrite($f, $s.PHP_EOL);
        fclose($f);
    }
// ---------------------------------------------------
//                D E B U G   O N L Y 
// ---------------------------------------------------
echo '1'.PHP_EOL;

$api = new ApiReport('2017', '05');
echo '2'.PHP_EOL;
$api->generate();

try {
//    $apiReport               = new ApiReport();
//    //$report                  = new ExcelSpout();
//    
//    $initDate                = (int)date('d');
//    $firstDay                = date('Y-m-01 00:00:00');
//    $today                   = date('Y-m-d 23:59:59');
//    $lastMonth               = date('m', strtotime($firstDay.' -1 month'));
//    $lastYear                = date('Y', strtotime($firstDay.' -1 month'));
//    $currentMonth            = date('m');
//    $currentYear             = date('Y');
//
//    $lastMonthDirectory      = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $lastYear   . '-' . $lastMonth . '/';
//    $lastMonthMessageDate    = $apiReport->getLastMessageDate($lastMonthDirectory);
//    
//    $generateLastMonth       = is_dir($lastMonthDirectory) && $initDate < 4;
//
//    $currentMonthDirectory   = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $currentYear. '-' . $lastMonth . '/';
//    $currentMonthMessageDate = $apiReport->getLastMessageDate($currentMonthDirectory);
//    
//    $users                   = $apiReport->getUserList();
//    $billingProfile          = $apiReport->loadBillingProfileCache($year, $month);
//
//    
//    foreach($users as $user) {
//        
//        if($user['BILLING_PROFILE_ID'] != $billingProfile->id ) {
//            $billing = $apiReport->getBilingProfileDetail($user['BILLING_PROFILE_ID']);
//            if($billing !== false) {
//                $billingProfile = (object) [
//                                    'id'      => $user['BILLING_PROFILE_ID'],
//                                    'type'    => $billing['BILLING_TYPE'],
//                                    'pricing' => $billing['BILLING_TYPE'] == $apiReport::BILLING_TIERING_BASE
//                                                    ? $apiReport->getTieringDetail($user['BILLING_PROFILE_ID'])
//                                                    : $apiReport->getOperatorBaseDetail($user['BILLING_PROFILE_ID'])
//                                ];
//            }
//            else {
//                $user->log->error('Could not find user billig profile, skip generate report for user "'.$user['USER_NAME'].'"');
//            }
//        }
//        
        
        
        // get Tiering Group
        // get Tiering Detail
        // 
        // 
        // get Report Group
        // 
        // Get Billing Detail
        // Get billing Pricing
        // Get User Message
        // Assign price into message
        // dump message to file
        
//        if ($generateLastMonth) {
//            do {
//                $messages = $apiReport->getUserMessageStatus(
//                                    $userId, 
//                                    $startDateTime, 
//                                    $endDateTime, 
//                                    REPORT_PER_BATCH_SIZE, $startIndex);
//                foreach($messages as &$message) {
//
//                }
//            } while
//        }
//       
        
        
//        do {
//            $messages = $apiReport->getUserMessageStatus(
//                                $userId, 
//                                $startDateTime, 
//                                $endDateTime, 
//                                REPORT_PER_BATCH_SIZE, 
//                                $startIndex);
//            foreach($messages as &$message) {
//                
//            }
//        } 
//        while(!empty($userMessage));
        
        
        
        
        echo '-----'.PHP_EOL;    
        echo '-----'.PHP_EOL;    
        echo '-----'.PHP_EOL;    
//    }
    
        //echo json_encode($apiReport->deliveryStatus, 192).PHP_EOL;
        $api
            ->queryHistory['TotalExecutionTime'] = 
                array_sum(
                    array_map(
                        function($a) {
                            return $a['executionTime'];
                        },
                        $api->queryHistory
                    )
                );
                        
        echo json_encode($api->queryHistory, 192).PHP_EOL;
        
        
        savePeformaceHistory($api->queryHistory['TotalExecutionTime']);
        
        
    exit();
//
//    $listClient      = $apiReport->getBillingClient();
//    
//    
//    $memory = []; 
//    foreach ($listClient as $client){
//        $idClient           = $client['CLIENT_ID'];
//        $listUser           = $apiReport->getUser($idClient);
//        $exportDataSpout    = new ExcelSpout();
//        
//
//        
//        foreach ($listUser as $user) {
//            $userId = $user['USER_NAME'];
//            //echo "$userId\n";
//            if($generateLastMonth) {
//                $lastUpdated = $exportDataSpout->getLastDateFromReport($userId, $lastMonth, $lastYear);
//                $lsReport    = $apiReport->getDataCronReport($userId, $lastMonth, $lastYear, $lastUpdated, true);
//                $exportDataSpout->getDataScheduled($userId, $lsReport, $lastMonth, $lastYear);
//            }
//            
//            if($initDate > 3){
//                $lastUpdated = $exportDataSpout->checkFile($userId, $currentMonth, $currentYear);
//                $lastUpdated = $lastUpdated !== false ? date("Y-m-d", strtotime("$lastUpdated +1 days")) : false;
//
//                $lsReport    = $apiReport->getDataCronReport($userId, $currentMonth, $currentYear, $lastUpdated);
//                $exportDataSpout->getDataScheduled($userId, $lsReport);
//                
//            }
//            $memory[] = memory_get_peak_usage(1);
//        }
//    }
//   
//    
//    // Package all billing report on previous month
//    if ($initDate < 4) {
//        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $lastYear . '-' . $lastMonth;
//        $logger->info('Packaging all report '.$lastYear.'-'.$lastMonth.' period');
//        $logger->info(exec('zip -j '.$directory.'/BillingReport_'.$lastYear.'-'.$lastMonth.'.zip '.$directory.'/*xlsx'));
//    }
//   
//    
//    // Package all billing report new 
//    $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $currentYear . '-' . $currentMonth;
//    $logger->info('Packaging all report for '.$currentYear.'-'.$currentMonth.' period');
//    $logger->info(exec('zip -j '.$directory.'/BillingReport_'.$currentYear.'-'.$currentMonth.'.zip '.$directory.'/*xlsx'));
//
//    
//    // calculating memory and time consuming for running this script
//    $memory       = round((array_sum($memory) / count($memory))/1024/1024, 2); 
//    $execute_time = microtime_float() - $execute_time;
//    $execute_time = substr($execute_time,0,4);
//
//    $logger->info("Finished generating billing report | Execution Time: ".$execute_time ."s | Memory: ".$memory."MB");
//   
} catch (Throwable $e) {
    $logger->error('generateCronReport Error: '.$e->getMessage());
    echo 'generateCronReport Error: '.$e->getMessage();
    echo $exc->getTraceAsString();
}

