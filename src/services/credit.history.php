<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUserCredit.php';

$page = SmsApiAdmin::getTemplate();
SmsApiAdmin::filterAccess();
SmsApiAdmin::loadConfig('transaction');
$tranCfg = SmsApiAdmin::getConfig('transaction');
try {
	if(empty ($_REQUEST['userID']))
		SmsApiAdmin::returnError('Missing user ID');
	$userID = $_REQUEST['userID'];
	$dataManager = new ApiUserCredit();
	$history = $dataManager->getTransactionHistory($userID);
	$page->assign('currencySign', $tranCfg['currencySign']);
	$page->assign('paymentMethod', $tranCfg['method']);
	$page->assign('history', $history);
	$page->display('credit.historyTable.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
	Logger::getRootLogger()->error("$e");
}