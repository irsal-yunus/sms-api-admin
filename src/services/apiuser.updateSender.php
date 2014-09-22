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

try {
	$senderID = filter_input(INPUT_POST, 'senderID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	$data = array();
	$data['senderName'] = filter_input(INPUT_POST, 'senderName', FILTER_VALIDATE_REGEXP, $senderNameValidationOpt);
	$data['senderRangeStart'] = isset ($_POST['senderRangeStart'])? $_POST['senderRangeStart'] : '';
	$data['senderRangeEnd'] = isset ($_POST['senderRangeEnd'])? $_POST['senderRangeEnd'] : '';

	if(Logger::getRootLogger()->isDebugEnabled()) {
		Logger::getRootLogger()->debug($_POST);
		Logger::getRootLogger()->debug($data);
	}

	if(!$data['senderName'])
		$errorFields['senderName'] = 'Invalid sender name';

	if($data['senderRangeStart'] != ''){//range start was set
		$data['senderRangeStart'] = filter_var($data['senderRangeStart'], FILTER_VALIDATE_REGEXP, $senderRangeValidationOpt);
		if(!$data['senderRangeStart']){
			$errorFields['senderRangeStart'] = 'Invalid sender range start';
		}
		if($data['senderRangeEnd'] != ''){
			$data['senderRangeEnd'] = filter_var($data['senderRangeEnd'], FILTER_VALIDATE_REGEXP, $senderRangeValidationOpt);
			if(!$data['senderRangeEnd']){
				$errorFields['senderRangeEnd'] = 'Invalid sender range end';
			}
		}
	}elseif($data['senderRangeEnd'] != ''){//range end was set
		$errorFields['senderRangeEnd'] = 'Setting range end without range start';
	}

	
	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}

	$model = new ApiUser();
	$model->updateSender($senderID, $data);

	$service->setStatus(true);
	$service->summarise('Success');
	$service->deliver();
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
}