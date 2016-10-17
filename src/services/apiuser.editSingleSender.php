<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
$senderID = filter_input(INPUT_POST, 'senderID', FILTER_VALIDATE_INT);
if(!$senderID)
	SmsApiAdmin::returnError ('Invalid senderID');
$model = new ApiUser();
$details = $model->getSenderDetails($senderID);
if(!$details)
	SmsApiAdmin::returnError ("Sender ID ($senderID) was not found");
$page->assign('senderID', $details['senderID']);
$page->assign('senderName', $details['senderName']);
$page->assign('senderRangeStart', $details['senderRangeStart']);
$page->assign('senderRangeEnd', $details['senderRangeEnd']);
$page->assign('cobranderID',$details['cobranderId']);
$page->assign('userID',$details['userID']);
$page->display('apiuser.editSenderForm.tpl');