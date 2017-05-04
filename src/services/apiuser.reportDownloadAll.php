<?php

/**
 * @author Basri.Y      
 * 
 * Copyright(c) 2017 1rstWAP. All rights reserved.
 * -----------------------------------------------
 * #18923   2017-05-03  Basri.Y     [Billing] Download All Reports
 */


require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiUser.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiBusinessClient.php';
$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    try {
        $page = SmsApiAdmin::getTemplate();
        $page->display('apiuser.reportDownloadAll.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Throwable $e) {
    $logger->error("$e");
}