<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
$clientModel = new ApiBusinessClient();
try {
	$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	if(!$userID)
		throw new InvalidArgumentException('Missing userID from arguments');
	$model = new ApiUser();
	$page->assign('userID', $userID);
	$page->assign('details', $model->getDetailsByID($userID));


// display it
        if(filter_has_var(INPUT_POST, 'clientID')){
                $clientID = filter_input(INPUT_POST, 'clientID', FILTER_VALIDATE_INT);
                if(empty($clientID))
                        SmsApiAdmin::returnError('Invalid client ID');
                $clientDetails = $clientModel->getDetails($clientID);
                $page->assign('clientLock', true);
                $page->assign('companyName',$clientDetails['companyName']);
                $page->assign('clientID',$clientID);
        }else{
                $clientList = $clientModel->getAllPaired();
                if(!$clientList)
                        SmsApiAdmin::returnError('Can not get client list');
                $page->assign('clientLock', false);
                $page->assign('clientList',$clientList);
                reset($clientList);
                $page->assign('clientID',key($clientList));
        }
                $page->display('apiuser.editAccountForm.tpl');
                
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}