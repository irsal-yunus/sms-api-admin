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
        if(!empty($_POST['billingProfileID']) && $_POST['mode'] == 'edit'){
           
            $billingProfileID   = $_POST['billingProfileID'];
            $billingType        = $_POST['billingType'];
            
            $apiReport          = new ApiReport();
           
            strtolower($billingType) == 'operator' 
                                        ? $page->assign('operatorSettings',$apiReport->getOperatorBaseDetail($billingProfileID))
                                        : $page->assign('tieringSettings',$apiReport->getTieringDetail($billingProfileID));
            $page->assign('billingProfileID', $billingProfileID);                   
            $page->assign('description', $apiReport->getBilingProfileDetail($billingProfileID));
            $page->assign('user',  $apiReport->getUserDetail(null,  $billingProfileID));
            $page->assign('mode', $_POST['mode']);
            
        }
        $page->assign('defaultOperator', ApiReport::DEFAULT_OPERATOR);
	$page->display('billing.new.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}