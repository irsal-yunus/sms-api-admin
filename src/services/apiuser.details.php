<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
$service->setStatus(false);
try {
	if(empty ($_POST['userID'])){
		$service->summarise('User ID can not be empty');
		$service->deliver();
	}else{
		$userID = $_POST['userID'];
		$dataManager = new ApiUser();
		if(!$dataManager->checkExistence($userID)){
			$service->summarise('User record was not found');
			$service->deliver();
		}
		$details = $dataManager->getDetailsByID($userID);
		if(!$details){
			throw new Exception("User (ID=$userID) record was not found");
		}else{
			$service->setStatus(true);
			$service->attachRaw($details);
			$service->deliver();
		}
	}
} catch (Exception $e) {
	$logger->error("$e");
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
}