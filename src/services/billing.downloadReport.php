<?php

/**
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 * 
 * @author Basri Yasin
 * -----------------------------------------------------------------------------------
 * #18802   2017-06-07  Basri.Y     [SMS Billing Report] Improve Performance & Tiering
 */


require_once dirname(__DIR__).'/init.d/init.php';
require_once dirname(__DIR__).'/lib/model/ApiReport.php';


try {
    
    $clientId   = $_GET['clientID'];
    $userId     = $_GET['userID'];
    $userName   = $_GET['userName'];
    $month      = $_GET['month'];
    $year       = $_GET['year'];
    $awaiting   = isset($_GET['sms_dr']);
    $check      = isset($_GET['check']);

    $api = new ApiReport($year, $month);
    
    if($check) {
        echo $api->isReportExist($awaiting, $userId)
                ? 'Exist'
                : 'File Doesn\'t Exist';
    }
    else {
        if($api->isReportExist($awaiting, $userId)) {
            $api->downloadReport($awaiting, $userId);
        }
        else {
            echo 'File Doesn\'t Exist';
        }
    }
    
} catch (Throwable $e) {
    $logger->error("generateReportUser error: ".$e->getMessage());
    SmsApiAdmin::returnError($e->getMessage());
}
