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
        $billingList = $apiReport->getBilingProfileDetail();
        if(!empty($_POST['tieringID']) && $_POST['mode'] == 'edit'){
           
            $tieringID   = $_POST['tieringID'];
            $page->assign('tieringGroupID', $tieringID);
            $page->assign('tieringDetail', $apiReport->getTieringGroupDetail($tieringID));
            $page->assign('user', $apiReport->getTieringGroupUserList($tieringID));
            $page->assign('mode',$_POST['mode']);
        }
        
        $page->assign('billingList', $billingList);
	$page->display('billing.newTiering.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}