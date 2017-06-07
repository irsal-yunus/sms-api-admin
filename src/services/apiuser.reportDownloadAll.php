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
        
        // Prepare list of months for billing report       
        $availablePeriods = [];
        for($year=date('Y'); $year > 2016; $year--){
            $startMonth = date('Y') == $year ? date('m') : 12;
            for($month  = $startMonth; $month>0; $month--){
                $availablePeriods[$year][$year.'-'.sprintf('%02d', $month)] = DateTime::createFromFormat('m', $month)->format('F');
            }
        }
        
        $page->assign('availablePeriods', $availablePeriods );
        $page->display('apiuser.reportDownloadAll.tpl');
        
    } 
    catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Throwable $e) {
    $logger->error("$e");
}