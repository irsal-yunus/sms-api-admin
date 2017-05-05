<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
//require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
try {
	$virtualNumberID = isset($_POST['virtualNumberID'])? $_POST['virtualNumberID'] : '';
	$model = new ApiUser();
	$page->assign('details', $model->getVirtualNumberDetails($virtualNumberID));
	$page->display('apiuser.editVirtualNumberForm.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}