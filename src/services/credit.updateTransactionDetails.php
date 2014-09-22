<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUserCredit.php';
SmsApiAdmin::filterAccess();
$tranCfg = SmsApiAdmin::getConfig('transaction');

$service = new AppJsonService();

try{
	$creditManager = new ApiUserCredit();
	$tranID = filter_input(INPUT_POST, 'creditTransactionID', FILTER_VALIDATE_INT);
	if(!$tranID)
		SmsApiAdmin::returnError('Missing transaction ID from arguments');
	$definitions = array(
		'transactionPrice' => array(
			'filter'=>FILTER_VALIDATE_FLOAT,
			'flags'=>FILTER_FLAG_ALLOW_FRACTION
		),
		'transactionCurrency' => FILTER_SANITIZE_STRING,
		'paymentMethod' => FILTER_SANITIZE_STRING,
		'transactionRequester' => FILTER_SANITIZE_STRING,
		'transactionRemark' => FILTER_SANITIZE_STRING
	);

	$transaction = filter_input_array(INPUT_POST, $definitions);

	$errorFields = array();

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
	if($transaction['paymentMethod']==''){
		$errorFields['paymentMethod'] = 'Payment method must be selected';
	}elseif(!array_key_exists($transaction['paymentMethod'], $tranCfg['method'])){
		$errorFields['paymentMethod'] = 'Unknown payment method: '.$transaction['paymentMethod'];
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

	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}

	if(!$creditManager->updateTransaction($tranID, $transaction))
		SmsApiAdmin::returnError('Transaction update was failed!');
	$service->setStatus(true);
	$service->deliver();
} catch(Exception $e){
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}
