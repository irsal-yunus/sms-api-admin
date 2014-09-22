<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	if(!$userID)
		throw new InvalidArgumentException('Missing userID from arguments');
	$model = new ApiUser();
	$page->assign('userID', $userID);
	$page->assign('details', $model->getDetailsByID($userID));
	$page->display('apiuser.editAccountForm.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}