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

$apiReport = new ApiReport();
$page->assign('billingList', $apiReport->getBilingProfileDetail());
$page->assign('tieringGroupList',$apiReport->getTieringGroupDetail());
$page->assign('reportGroupList',$apiReport->getReportGroupDetail());
$page->display('billing.view.tpl');