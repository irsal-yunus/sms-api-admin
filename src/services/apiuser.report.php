<?php

/**
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 * 
 * @author Basri Yasin
 * -----------------------------------------------------------------------------------
 * #18802   2017-06-07  Basri.Y     [SMS Billing Report] Improve Performance & Tiering
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiUser.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiBusinessClient.php';
$logger = Logger::getLogger("service");

try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();
    try {
        
        if (isset($_REQUEST['userID'])) {
            $userID = $_REQUEST['userID'];
        } else {
            throw new InvalidArgumentException('Missing userID from arguments');
        }
        
        $model = new ApiUser();
        if (!$model->checkExistence($userID)) throw new Exception('User not found');
        
        $page->assign('userID', $userID);
        $page->assign('details', $model->getDetailsByID($userID));
        $page->display('apiuser.report.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Throwable $e) {
    $logger->error($e);
}