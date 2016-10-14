<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
$errorFields = array();
$userConfig = SmsApiAdmin::getConfig('user');
$senderRangeValidationOpt = array ('options'=> array('regexp'=>  $userConfig['senderRangePattern']));
$senderNameValidationOpt = array ('options'=> array('regexp'=>  $userConfig['senderNamePattern']));

try{
	$userID = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_NUMBER_INT, array('options'=>array('min_range'=>1)));
	if($userID===null){
		SmsApiAdmin::returnError('Missing userID from arguments');
	}elseif($userID===false){
		SmsApiAdmin::returnError('Invalid userID');
	}
	$senderName = filter_input(INPUT_POST, 'senderName', FILTER_VALIDATE_REGEXP, $senderNameValidationOpt);
	$rangeStart = isset($_POST['senderRangeStart'])? trim($_POST['senderRangeStart']) : '';
	$rangeEnd = isset($_POST['senderRangeEnd'])? trim($_POST['senderRangeEnd']) : '';
        $cobranderId = filter_input(INPUT_POST, 'cobranderId',FILTER_UNSAFE_RAW);
	$enabled = filter_input(INPUT_POST, 'senderEnabled', FILTER_VALIDATE_BOOLEAN);

	if(!$senderName)
		$errorFields['senderName'] = 'Sender name can not not be empty';

	if($rangeStart != ''){//range start was set
		$rangeStart = filter_var($rangeStart, FILTER_VALIDATE_REGEXP, $senderRangeValidationOpt);
		if(!$rangeStart){
			$errorFields['senderRangeStart'] = 'Invalid sender range start';
		}
		if($rangeEnd != ''){
			$rangeEnd = filter_var($rangeEnd, FILTER_VALIDATE_REGEXP, $senderRangeValidationOpt);
			if(!$rangeEnd){
				$errorFields['senderRangeEnd'] = 'Invalid sender range end';
			}
		}
	}elseif($rangeEnd != ''){//range end was set
		$errorFields['senderRangeEnd'] = 'Setting range end without range start';
	}
	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}

	$model = new ApiUser();
	$senderID = $model->addSender($userID, $senderName, $rangeStart, $rangeEnd, $cobranderId, $enabled);
	$service->setStatus(true);
	$service->attach('senderID', $senderID);
	$service->deliver();
} catch(Exception $e){
	SmsApiAdmin::returnError($e->getMessage());
}
