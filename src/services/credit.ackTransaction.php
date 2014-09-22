<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUserCredit.php';
SmsApiAdmin::filterAccess();
SmsApiAdmin::loadConfig('transaction');
$tranCfg = SmsApiAdmin::getConfig('transaction');

$service = new AppJsonService();

try{
	$creditManager = new ApiUserCredit();
	$tranID = filter_input(INPUT_POST, 'creditTransactionID', FILTER_VALIDATE_INT);
	if(!$tranID)
		SmsApiAdmin::returnError('Missing transaction ID from arguments');
	$definitions = array(
		'paymentDate' => array (
			'filter'=>FILTER_SANITIZE_STRING,
			'flags'=>FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
		)
	);

	$inquiry = filter_input_array(INPUT_POST, $definitions);

	$errorFields = array();
	if($inquiry['paymentDate'] === null){
		$errorFields['paymentDate'] = 'Payment date must be set!';
	}elseif($inquiry['paymentDate'] === false){
		$errorFields['paymentDate'] = 'Invalid payment date!';
	}


	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}

	if(!$creditManager->acknowledgePayment($tranID, $inquiry))
		SmsApiAdmin::returnError('Transaction was failed!');
	$service->setStatus(true);
	$service->deliver();
} catch(Exception $e){
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}
