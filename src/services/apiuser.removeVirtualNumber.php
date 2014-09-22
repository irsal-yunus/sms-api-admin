<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';

SmsApiAdmin::filterAccess();
$service = new AppJsonService();
try {
	$virtualNumberID= isset($_POST['virtualNumberID'])? $_POST['virtualNumberID'] : null;
	$dataManager = new ApiUser();
	$dataManager->removeVirtualNumber($virtualNumberID);
	$service->setStatus(true);
	$service->deliver();
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}