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
	$senderID = isset($_POST['senderID'])? $_POST['senderID'] : null;
	$model = new ApiUser();
	$senderID = $model->disableSender($senderID);
	$service->setStatus(true);
	$service->deliver();
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}