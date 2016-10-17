<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUserCredit.php';

SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
SmsApiAdmin::loadConfig('transaction');
$tranCfg = SmsApiAdmin::getConfig('transaction');
try {
	$userID = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_NUMBER_INT, array('options'=>array('min_range'=>1)));
	if(!$userID)
		SmsApiAdmin::returnError('Invalid user ID');
	$apiuser = new ApiUser();
	$apiuser->checkExistence($userID, true);
	$page->assign('user', $apiuser->getDetailsByID($userID));
//	$page->assign('defaultPaymentMethod', $tranCfg['defaultMethod']);
//	$page->assign('paymentMethods', $tranCfg['method']);
//	$page->assign('defaultCurrency', $tranCfg['defaultCurrency']);
//	$page->assign('currencyList', $tranCfg['currency']);
	$page->display('credit.deductionForm.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}