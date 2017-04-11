<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';

SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	if(empty($_REQUEST['userID']))
		throw "Empty user ID";
	if(!is_numeric($_REQUEST['userID']))
		throw "Invalid user ID ({$_REQUEST['userID']})";
	$userID = $_REQUEST['userID'];
	$dataManager = new ApiUser();
	$page->assign('details', $dataManager->getDetailsByID($userID));
	$page->display('apiuser.viewBasic.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}


