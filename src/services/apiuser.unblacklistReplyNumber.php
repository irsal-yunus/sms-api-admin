<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
if(!$userID)
	SmsApiAdmin::returnError ('Invalid user ID');

$userConfig = SmsApiAdmin::getConfig('user');

$isValid = filter_input(INPUT_POST, 'msisdn', FILTER_VALIDATE_REGEXP, array(
				'options'=>
					array(
						'regexp'=>  $userConfig['msisdnPattern']
					)
				));
if(!$isValid)
	SmsApiAdmin::returnError ('Invalid MSISDN!');

$dataManager = new ApiUser();
$dataManager->unblacklistReplyNumber($userID, $msisdn);
$service->setStatus(true);
$service->summarise('The number has successfully removed from blacklist');
$service->deliver();