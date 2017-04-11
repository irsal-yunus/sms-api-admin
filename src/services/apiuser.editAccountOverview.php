<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	if(!isset ($_REQUEST['userID']))
		SmsApiAdmin::returnError('Missing userID from arguments');
	$userID = $_REQUEST['userID'];
	$model = new ApiUser();
	$page->assign('userID', $userID);
	$page->assign('details', $model->getDetailsByID($userID));
	$page->display('apiuser.editAccountOverview.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}