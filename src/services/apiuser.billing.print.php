<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 * 
 * 
 */

/**
 * @author Fathir Wafda
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';
require_once '../lib/FirePHPCore/FirePHP.class.php';

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
$firephp = FirePHP::getInstance(true);

try { 
    
        $clientID = filter_input(INPUT_POST, 'clientID', FILTER_VALIDATE_INT);
        $userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
        if(empty ($userID)){
		$service->setStatus(false);
		$service->summarise("Invalid user ID (ID=$userID)");
		$service->deliver();
	}
	if(empty ($clientID)){
		$service->setStatus(false);
		$service->summarise("Invalid client ID (ID=$clientID)");
		$service->deliver();
	}
        
	$errorFields = array();
	$definitions = array(
                'clientID'=>FILTER_SANITIZE_NUMBER_INT,
		'companyName'=>FILTER_SANITIZE_STRING,
                'year'=>FILTER_SANITIZE_STRING,
                'month'=>FILTER_SANITIZE_STRING,
                'fromDate' => array (
			'filter'=>FILTER_SANITIZE_STRING,
			'flags'=>FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
		),
                'endDate' => array (
			'filter'=>FILTER_SANITIZE_STRING,
			'flags'=>FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW
		)             
                          
            
	);
	$clientData = filter_input_array(INPUT_POST, $definitions);
        $clientDetail = new ApiBusinessClient();
        $clientGet = $clientDetail->getBillingDetails($clientID);
        
	foreach($clientData as $key=>$value)
		if($value===null)
			unset($clientData[$key]);
	if(!$clientData){
		$service->setStatus(false);
		$service->summarise('No data fields');
		$service->deliver();
	}
	
	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
        
        }
//		$clientModel = new ApiBusinessClient();
//		$clientModel->insertBilling($clientID, $clientData);
		$service->setStatus(true);
		$service->summarise('Report has printed..');
		$service->deliver();
	
} catch (Exception $e) {
	$logger->error("$e");
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
}