<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

SmsApiAdmin::filterAccess();
$service = new AppJsonService();
try {
	$clientID = filter_input(INPUT_POST, 'clientID', FILTER_VALIDATE_INT);
	if(empty($clientID)){
		$service->setStatus(false);
		$service->summarise('Empty client ID');
		$service->deliver();
	}
	$clientModel = new ApiBusinessClient();
	if(!$clientModel->checkExistence($clientID))
		SmsApiAdmin::returnError('Client record was not found');
	$clientModel->delete($clientID);
	$service->setStatus(true);
	$service->deliver();
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
}