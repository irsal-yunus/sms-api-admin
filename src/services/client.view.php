<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$template = SmsApiAdmin::getTemplate();
try {
	$clientID = filter_input(INPUT_POST, 'clientID');
	if(empty($clientID))
		throw new Exception("Invalid client ID (ID=$clientID)!");
	$dataManager = new ApiBusinessClient();
	if(!$dataManager->checkExistence($clientID))
		SmsApiAdmin::returnError('Can not find inquired client record!');
	$details = $dataManager->getDetails($clientID);
	if(!$details)
		SmsApiAdmin::returnError("No data for clientID=$clientID");
	$template->assign('client', $details);
	$template->display('client.view.tpl');
} catch (Exception $e) {
	$logger->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}
