<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';
$logger = Logger::getLogger("service");
try {
	SmsApiAdmin::filterAccess();
	$page = SmsApiAdmin::getTemplate();
	try {
		if(isset ($_REQUEST['userID'])){
			$userID = $_REQUEST['userID'];
		}else{
			throw new InvalidArgumentException('Missing userID from arguments');
		}
		$model = new ApiUser();
		if(!$model->checkExistence($userID))
			throw new Exception('User not found');
		$page->assign('userID', $userID);
                $page->assign('year', $year);
                $page->assign('details', $model->getDetailsByID($userID));
                $page->assign('sms_dr', $sms_dr);
                $page->assign('month', $month);
//                $page->assign('client', $clientModel->getDetails($clientID));
//                $page->assign('billing', $clientModel->getBillingDetails($clientID));
		$page->display('apiuser.report.tpl');
	} catch (Exception $e) {
		SmsApiAdmin::returnError($e->getMessage());
	}
} catch (Exception $e) {
	$logger->error("$e");
}