<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiReport.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();

try {
        $apiReport = new ApiReport();
        if(!empty($_POST['reportID']) && $_POST['mode'] == 'edit'){
            $reportGroupID   = $_POST['reportID'];
            $page->assign('reportGroupID', $reportGroupID);
            $page->assign('reportDetail', $apiReport->getReportGroupDetail($reportGroupID));
            $page->assign('user', $apiReport->getReportGroupUserList($reportGroupID));
            $page->assign('mode', $_POST['mode']);
        }
        
        $page->assign('billingList', $apiReport->getBilingProfileDetail());
	$page->display('billing.newReport.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}