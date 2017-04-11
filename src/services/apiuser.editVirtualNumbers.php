<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	if(!empty($_REQUEST['userID'])){
		$userID = trim($_REQUEST['userID']);
	}else{
		throw new InvalidArgumentException('Missing userID from arguments');
	}
	$model = new ApiUser();
	$page->assign('userID', $userID);
	$page->assign('virtualNumber', $model->getUserVirtualNumbers($userID));
	$page->display('apiuser.editVirtualNumbers.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}