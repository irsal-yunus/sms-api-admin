<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';

SmsApiAdmin::filterAccess();
$service = new AppJsonService();
try {
	$errorFields = array();
	$definitions = array(
		'userName' => array(
			'filter'=>FILTER_VALIDATE_REGEXP,
			'options'=>array(
				'regexp'=>  SmsApiAdmin::getConfigValue('user', 'userNamePattern')
			),
		),
		'userPassword' => FILTER_SANITIZE_STRING,
		'clientID' => FILTER_VALIDATE_INT,
		'cobranderID' => FILTER_SANITIZE_STRING,
		'active' => FILTER_VALIDATE_BOOLEAN,
		'replyBlacklistEnabled' => FILTER_VALIDATE_BOOLEAN,
		'isPostpaid' => FILTER_VALIDATE_BOOLEAN,
		'expiredDate' => FILTER_SANITIZE_STRING,
		'statusDeliveryActive' => FILTER_VALIDATE_INT,
		'statusDeliveryUrl' => array(
			'filter'=>FILTER_VALIDATE_URL,
			'options'=>array(
				'flag'=>  FILTER_FLAG_SCHEME_REQUIRED
			),
		)
	);
	$regData = filter_input_array(INPUT_POST, $definitions);
	if(!$regData){
		$service->setStatus(false);
		$service->summarise('No data fields');
		$service->deliver();
	}
	$regData['statusDeliveryActive'] = $regData['statusDeliveryActive']? 1 : 0;
	if($regData['statusDeliveryActive']){
		$regData['statusDeliveryUrl'] = trim($regData['statusDeliveryUrl']);
		if(false === filter_var($regData['statusDeliveryUrl'], FILTER_VALIDATE_URL))
			$errorFields['statusDeliveryUrl']='Invalid status delivery URL';
	}else{
		$regData['statusDeliveryUrl'] = null;
	}
	if($regData['userName']===null){
		$errorFields['userName']= 'User name must be set!';
	}elseif($regData['userName']===false){
		$errorFields['userName']= 'Invalid user name';
	}
	if($regData['userPassword']=='')
		$errorFields['userPassword']= 'Password must be set!';
	if(empty($regData['clientID']))
		$errorFields['clientID']= 'Client must be set!';
	if(empty($regData['expiredDate']))
		$errorFields['expiredDate']= 'Expired Date must be set!';
        
        $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
        $dateNow= date('Y-m-d H:i:s',$tomorrow);
        if(!empty($regData['expiredDate']) && $_POST['expiredDate'] < $dateNow)
                $errorFields['expiredDate']= 'Expired date must be greater then today';


	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}
	$dataManager = new ApiUser();
	$userID=$dataManager->register($regData);
	if(false === $userID){
		SmsApiAdmin::returnError('Failed processing user registration');
	}else{
		$service->setStatus(true);
		$service->attach('userID', $userID);
		$service->attach('clientID', $regData['clientID']);
	}
	$service->deliver();
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}