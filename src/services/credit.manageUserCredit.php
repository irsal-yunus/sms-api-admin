<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUserCredit.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
//SmsApiAdmin::loadConfig('transaction');
//$tranCfg = SmsApiAdmin::getConfig('transaction');
try {
	$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	if(empty($userID))
		throw new Exception('Invalid user ID');
	$apiuser = new ApiUser();
	$apiuser->checkExistence($userID, true);
	$page->assign('userID', $userID);
	$page->assign('user', $apiuser->getDetailsByID($userID));
	$page->display('credit.manager.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}
