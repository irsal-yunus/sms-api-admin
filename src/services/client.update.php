<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiCountry.php';

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
try {
	$clientID = filter_input(INPUT_POST, 'clientID', FILTER_VALIDATE_INT);
	if(empty ($clientID)){
		$service->setStatus(false);
		$service->summarise("Invalid client ID (ID=$clientID)");
		$service->deliver();
	}
	$errorFields = array();
	$definitions = array(
		'companyName'=>FILTER_SANITIZE_STRING,
		'companyUrl'=>FILTER_SANITIZE_STRING,
		'countryCode'=>FILTER_SANITIZE_STRING,
		'contactName'=>FILTER_SANITIZE_STRING,
		'contactEmail'=>FILTER_SANITIZE_STRING,
		'contactPhone'=>FILTER_SANITIZE_STRING
	);
	$updates = filter_input_array(INPUT_POST, $definitions);
	foreach($updates as $key=>$value)
		if($value===null)
			unset($updates[$key]);
	if(!$updates){
		$service->setStatus(false);
		$service->summarise('No update fields');
		$service->deliver();
	}
	if($updates['companyName']=='')
		$errorFields['companyName']='Company name should not be empty!';
	if($updates['contactName']=='')
		$errorFields['contactName']='Contact name should not be empty!';
	if(($updates['contactEmail']!='') && !filter_var($updates['contactEmail'], FILTER_VALIDATE_EMAIL))
		$errorFields['contactEmail']='Invalid email address!';
	if(($updates['companyUrl']!='') && !filter_var($updates['companyUrl'], FILTER_VALIDATE_URL))
		$errorFields['companyUrl']='Invalid company URL!';
	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}else{
		$clientModel = new ApiBusinessClient();
		$clientModel->update($clientID, $updates);
		$service->setStatus(true);
		$service->summarise('Client data successfully updated');
		$service->deliver();
	}
} catch (Exception $e) {
	$logger->error("$e");
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
}