<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
if(!$userID)
	SmsApiAdmin::returnError ('Invalid user ID');
$ipAddress= filter_input(INPUT_POST, 'ipAddress', FILTER_VALIDATE_IP);
if(!$ipAddress)
	SmsApiAdmin::returnError ('Invalid IP address!');
$dataManager = new ApiUser();
$dataManager->unsetIPPermission($userID, $ipAddress);
$service->setStatus(true);
$service->summarise('IP permission was successfully unset');
$service->deliver();