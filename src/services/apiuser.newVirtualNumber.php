<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';

$page = SmsApiAdmin::getTemplate();
$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
if(!$userID)
	SmsApiAdmin::returnError ('Invalid user ID');
$page->assign('userID', $userID);
$page->display('apiuser.regVirtualNumber.tpl');
