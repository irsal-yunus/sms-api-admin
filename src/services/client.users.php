<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	if(!isset($_REQUEST['clientID']))
		throw 'Missing client ID from arguments!';
	$clientID = filter_var($_REQUEST['clientID'], FILTER_VALIDATE_INT);
	if(empty($clientID))
		throw 'Empty client ID';
	$apiuserManager = new ApiUser();
	$users = $apiuserManager->getAllClientUsers($clientID);
	$page->assign('users', $users);
	$page->assign('clientID', $clientID);
	$page->display('client.userTable.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}