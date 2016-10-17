<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';

SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	if(empty($_POST['userID']))
		throw "Empty user ID";
	if(!is_numeric($_POST['userID']))
		throw "Invalid user ID ({$_POST['userID']})";
	$userID = $_POST['userID'];
	$dataManager = new ApiUser();
	$page->assign('details', $dataManager->getDetailsByID($userID));
	$page->assign('senderID', $dataManager->getUserSenderID($userID));
	$page->assign('permittedIP', $dataManager->getUserIP($userID));
	$page->assign('virtualNumber', $dataManager->getUserVirtualNumbers($userID));
//	$page->assign('replyBlacklist', $dataManager->getUserReplyBlacklist($userID));
	$page->display('apiuser.view.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}


