<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
try {
	$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
	if($userID===null){
		SmsApiAdmin::returnError('Missing user ID in arguments');
	}elseif($userID===false){
		SmsApiAdmin::returnError('Invalid user ID');
	}
	$apiUser = new ApiUser();
	if(!$apiUser->deactivateUser($userID))
		SmsApiAdmin::returnError('Failed deactivating user');
	$service->setStatus(true);
	$service->deliver();
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}