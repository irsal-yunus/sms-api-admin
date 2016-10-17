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
$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
if(!$userID)
	throw new InvalidArgumentException('Missing userID from arguments');
$userModel = new ApiUser();
if(!$userModel->checkExistence($userID)){
	Logger::getRootLogger()->warn("Attempt to add credit for User with ID=$userID which is not exist");
	SmsApiAdmin::returnError("User not found");
}

$definitions = array(
	'transactionCredit' => FILTER_VALIDATE_INT,
	'transactionPrice' => array(
		'filter'=>FILTER_VALIDATE_FLOAT,
		'flags'=>FILTER_FLAG_ALLOW_FRACTION
	),
	'transactionCurrency' => FILTER_SANITIZE_STRING,
	'paymentMethod' => FILTER_SANITIZE_STRING,
	'transactionRequester' => array(
		'filter'=>FILTER_SANITIZE_STRING,
		'flags'=>FILTER_FLAG_STRIP_LOW
	),
	'transactionRemark' => FILTER_SANITIZE_STRING
);

$transaction = filter_input_array(INPUT_POST, $definitions);

$errorFields = array();
if($transaction['transactionCredit'] === null){
	$errorFields['transactionCredit'] = 'Credit amount must be set!';
}elseif($transaction['transactionCredit'] === false){
	$errorFields['transactionCredit'] = 'Invalid credit amount!';
}elseif((int) $transaction['transactionCredit'] <= 0){
	$errorFields['transactionCredit'] = 'Credit amount must be greater than zero!';
}

if($transaction['transactionPrice'] === null){
	$errorFields['transactionPrice'] = 'Price must be set!';
}elseif($transaction['transactionPrice'] === false){
	$errorFields['transactionPrice'] = 'Invalid price!';
}elseif((float) $transaction['transactionPrice'] < 0){
	$errorFields['transactionPrice'] = 'Price can not be negative!';
}

if($transaction['transactionCurrency']==''){
	$errorFields['transactionCurrency'] = 'Currency code must be selected';
}elseif(!array_key_exists($transaction['transactionCurrency'], $tranCfg['currency'])){
	$errorFields['transactionCurrency'] = 'Unknown currency code: '.$transaction['transactionCurrency'];
}

if($transaction['transactionRequester']===null){
	$errorFields['transactionRequester'] = 'Missing requester name!';
}elseif($transaction['transactionRequester']===false){
	$errorFields['transactionRequester'] = 'Invalid requester name!';
}else{
	$transaction['transactionRequester'] = trim($transaction['transactionRequester']);
	if($transaction['transactionRequester']==''){
		$errorFields['transactionRequester'] = 'Requester name can not be empty!';
	}
}

if($transaction['paymentMethod']==''){
	$errorFields['paymentMethod'] = 'Payment method must be selected';
}elseif(!array_key_exists($transaction['paymentMethod'], $tranCfg['method'])){
	$errorFields['paymentMethod'] = 'Unknown payment method: '.$transaction['paymentMethod'];
}

if($errorFields){
	$service->setStatus(false);
	$service->summarise('Input fields error');
	$service->attachRaw($errorFields);
	$service->deliver();
}

$creditManager = new ApiUserCredit();
$transactionID = $creditManager->topUp($userID, $transaction);
if(!$transactionID)
	SmsApiAdmin::returnError('Transaction was failed!');
$service->setStatus(true);
$service->attach('creditTransactionID', $transactionID);
$service->deliver();