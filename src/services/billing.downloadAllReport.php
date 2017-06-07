<?php

/**
 * @author Basri.Y      
 * 
 * Copyright(c) 2017 1rstWAP. All rights reserved.
 * -----------------------------------------------
 * #18923   2017-05-03  Basri.Y     [Billing] Download All Reports
 * #18802   2017-05-08  Basri.Y     [SMS Billing Report] Improve Performance & Tiering
 */

require_once dirname(__DIR__).'/init.d/init.service.php';
require_once dirname(__DIR__).'/init.d/init.php';
require_once dirname(__DIR__).'/lib/model/ApiReport.php';

try {
    
    /**
     * Initialize and validate user input
     */
    $period    = isset($_GET['period'])  ? $_GET['period'] : false;
    $awaiting  = isset($_GET['sms_dr']);
    $check     = isset($_GET['check']);
    
    if($period == false) {
        die('File Doesn\'t Exist');
    }
    
    
    /**
     * Initialize Api Report
     */
    list($year, $month) = explode('-', $period);
    $api = new ApiReport($year, $month);
    /**
     * execute user input
     */
    if ($check) {
        echo $api->isReportExist($awaiting) ? 'Exist' : 'File Doesn\'t Exist';
    }
    else if( $api->isReportExist($awaiting) ) { 
        $api->downloadReport($awaiting);
    }
    else {
        echo 'File Doesn\'t Exist';
    }
    
    
} catch (Throwable $e) {
    $api->log->error('DownloadAllBillingReport error: '.$e->getMessage());
    SmsApiAdmin::returnError($e->getMessage());
}
