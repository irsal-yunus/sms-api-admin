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
if(!isset($_POST['userPassword']))
	SmsApiAdmin::returnError ('Missing password in arguments');
$password = $_POST['userPassword'];
$dataManager = new ApiUser();
if($dataManager->changePassword($userID, $password)){
	$service->setStatus(true);
	$service->deliver();
}else{
	$service->setStatus(false);
	$service->summarise('Operation failed!');
	$service->deliver();
}