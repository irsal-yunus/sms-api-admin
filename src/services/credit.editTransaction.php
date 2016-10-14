<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUserCredit.php';

SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
SmsApiAdmin::loadConfig('transaction');
$tranCfg = SmsApiAdmin::getConfig('transaction');
try {
	$tranID = filter_input(INPUT_POST, 'creditTransactionID', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
	if(!$tranID)
		SmsApiAdmin::returnError('Invalid transaction ID');
	$creditManager = new ApiUserCredit();
	if(!$creditManager->checkIsTransactionEditable($tranID))
		SmsApiAdmin::returnError('Closed transaction is not editable');
	$page->assign('transaction', $creditManager->getTransactionDetailsByID($tranID));
	$page->assign('paymentMethods', $tranCfg['method']);	
	$page->assign('currencyList', $tranCfg['currency']);
	$page->display('credit.editTransactionForm.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}