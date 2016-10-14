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
	$tranID = filter_input(INPUT_POST,
					'creditTransactionID',
					FILTER_VALIDATE_INT,
					array('options'=>array('min_range'=>1)));
	if(!$tranID)
		SmsApiAdmin::returnError("Invalid transaction ID ($tranID)");
	$creditManager = new ApiUserCredit();
	$transaction = $creditManager->getTransactionDetailsByID($tranID);
	if(!$transaction)
		SmsApiAdmin::returnError('Transaction record not found!');
	
	$page->assign('transaction', $transaction);
	$page->assign('paymentMethods', $tranCfg['method']);
	$page->assign('currencyList', $tranCfg['currency']);
	$page->display('credit.paymentForm.tpl');
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}