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
$user  			= $userModel->getDetailsByID($userID);
$previousBalance= $user['userCredit'];

if(!$userModel->checkExistence($userID)){
	Logger::getRootLogger()->warn("Attempt to add credit for User with ID=$userID which is not exist");
	SmsApiAdmin::returnError("User not found");
}
$user  			= $userModel->getDetailsByID($userID);
$previousBalance= $user['userCredit'];

$definitions = array(
	'transactionCredit' => FILTER_VALIDATE_INT,
	'transactionRemark' => FILTER_SANITIZE_STRING,
	'previousBalance'   => 0,
	'currentBalance'    => 0,
);

$deduction = filter_input_array(INPUT_POST, $definitions);
$deduction['previousBalance'] = $previousBalance;
$deduction['currentBalance']  = $deduction['previousBalance']-$deduction['transactionCredit'];


$errorFields = array();
if($deduction['transactionCredit'] === null){
	$errorFields['transactionCredit'] = 'Credit amount must be set!';
}elseif($deduction['transactionCredit'] === false){
	$errorFields['transactionCredit'] = 'Invalid credit amount!';
}elseif((int) $deduction['transactionCredit'] <= 0){
	$errorFields['transactionCredit'] = 'Credit amount must be greater than zero!';
}

if(trim($deduction['transactionRemark'])===''){
	$errorFields['transactionRemark'] = 'Remark must be given!';
}

if($errorFields){
	$service->setStatus(false);
	$service->summarise('Input fields error');
	$service->attachRaw($errorFields);
	$service->deliver();
}

$creditManager = new ApiUserCredit();
$transactionID = $creditManager->deduct($userID, $deduction);
if(!$transactionID)
	SmsApiAdmin::returnError('Transaction was failed!');
$service->setStatus(true);
$service->attach('creditTransactionID', $transactionID);
$service->deliver();