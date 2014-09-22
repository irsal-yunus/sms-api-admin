<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	$userID = isset($_REQUEST['userID'])? $_REQUEST['userID'] : null;
	$model = new ApiUser();
	$page->assign('userID', (int) $userID);
	$page->assign('permittedIP', $model->getUserIP($userID));
	$page->display('apiuser.editIP.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}