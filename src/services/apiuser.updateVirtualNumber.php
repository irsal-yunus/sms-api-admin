<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$logger = Logger::getRootLogger();
$service = new AppJsonService();
try {
	$virtualNumberID = isset ($_POST['virtualNumberID'])? $_POST['virtualNumberID'] : '';
	$data = array();
	$data['virtualDestination'] = filter_input(INPUT_POST, 'virtualDestination', FILTER_VALIDATE_REGEXP,
							array('options'=>array('regexp'=>'/^(\d+)$/')));
	$data['virtualUrlActive'] = filter_input(INPUT_POST, 'virtualUrlActive', FILTER_VALIDATE_BOOLEAN);
	$data['virtualUrl'] = filter_input(INPUT_POST, 'virtualUrl', FILTER_VALIDATE_URL,
							array('options'=>array('flags'=>FILTER_FLAG_SCHEME_REQUIRED)));
	$errorFields = array();
	if($data['virtualDestination']===null){
		$errorFields['virtualDestination'] = 'Destination must be set';
	}elseif($data['virtualDestination']===false){
		$errorFields['virtualDestination'] = 'Invalid destination number';
	}
	if($data['virtualUrlActive']){
		if($data['virtualUrl'] === null){
			$errorFields['virtualUrl'] = 'Forward url can not be empty';
		}elseif($data['virtualUrl'] === false){
			$errorFields['virtualUrl'] = 'Invalid forward url';
		}
	}
	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}

	$model = new ApiUser();
	$model->updateVirtualNumber($virtualNumberID, $data);
	$service->setStatus(true);
	$service->deliver();
} catch (Exception $e) {
	$logger->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}