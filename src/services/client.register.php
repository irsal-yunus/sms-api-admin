<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

SmsApiAdmin::filterAccess();
$service = new AppJsonService();
try {
	$errorFields = array();
	$definitions = array(
		'customerId'=>FILTER_SANITIZE_STRING,
		'companyName'=>FILTER_SANITIZE_STRING,
		'companyUrl'=>FILTER_SANITIZE_STRING,
		'countryCode'=>FILTER_SANITIZE_STRING,
		'contactName'=>FILTER_SANITIZE_STRING,
		'contactEmail'=>FILTER_SANITIZE_STRING,
		'contactPhone'=>FILTER_SANITIZE_STRING,
		'contactAddress'=>FILTER_SANITIZE_STRING
	);
	$clientData = filter_input_array(INPUT_POST, $definitions);
	foreach($clientData as $key=>$value)
		if($value===null)
			unset($clientData[$key]);
	if(!$clientData){
		$service->setStatus(false);
		$service->summarise('No data fields');
		$service->deliver();
	}
	if($clientData['customerId']=='')
		$errorFields['customerId']='Customer ID should not be empty!';
	if($clientData['companyName']=='')
		$errorFields['companyName']='Company name should not be empty!';
	if($clientData['contactName']=='')
		$errorFields['contactName']='Contact name should not be empty!';
	if(($clientData['contactEmail']!='') && !filter_var($clientData['contactEmail'], FILTER_VALIDATE_EMAIL))
		$errorFields['contactEmail']='Invalid email address!';
	if(($clientData['companyUrl']!='') && !filter_var($clientData['companyUrl'], FILTER_VALIDATE_URL))
		$errorFields['companyUrl']='Invalid company URL!';

	if ($errorFields) {
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	} else {
		$clientModel = new ApiBusinessClient();
		$clientID = $clientModel->register($clientData);
		$service->setStatus(true);
		$service->attach('clientID', $clientID);
		$service->deliver();
	}
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}
