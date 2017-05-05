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
	$tranID = filter_input(INPUT_POST,
						'creditTransactionID', 
						FILTER_VALIDATE_INT,
						array(
							'options'=>array('min_range'=>1)
						)
					);
	if($tranID===null){
		SmsApiAdmin::returnError('Missing transaction ID!');
	}elseif($tranID===false){
		SmsApiAdmin::returnError('Invalid transaction ID!');
	}
	$creditManager = new ApiUserCredit();
	$page->assign('details', $creditManager->getTransactionDetailsByID($tranID));
	$page->assign('undefinedMethodDesc', $tranCfg['undefinedMethodDesc']);
	$page->assign('paymentMethods', $tranCfg['method']);
	$page->assign('currencyDesc', $tranCfg['currency']);
	$page->display('credit.viewTransaction.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}


