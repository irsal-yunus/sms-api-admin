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
	if(empty ($clientID)){
		$service->setStatus(false);
		$service->summarise("Invalid client ID (ID=$clientID)");
		$service->deliver();
	}
        
	$errorFields = array();
	$definitions = array(
                'clientID'=>FILTER_SANITIZE_NUMBER_INT,
		'companyName'=>FILTER_SANITIZE_STRING,
                'billsms'=> array(
                     'filter' => FILTER_SANITIZE_STRING,
                     'flags'  => FILTER_REQUIRE_ARRAY
                    ),
		'subIdBillNo'=>FILTER_SANITIZE_STRING,
		'errorCode'=>FILTER_SANITIZE_STRING,
		'unknown'=>FILTER_SANITIZE_NUMBER_INT,
		'pending'=>FILTER_SANITIZE_NUMBER_INT,
                'undelivered'=>FILTER_SANITIZE_NUMBER_INT,
                'showDelivered'=>FILTER_SANITIZE_NUMBER_INT,
                'deliveredDesc'=>FILTER_SANITIZE_STRING,
                'totalSms'=>FILTER_SANITIZE_NUMBER_INT,
                'totalCharge'=>FILTER_SANITIZE_NUMBER_INT,
                'showProvider'=>FILTER_SANITIZE_NUMBER_INT,
                'provided'=> array(
                     'filter' => FILTER_SANITIZE_STRING,
                     'flags'  => FILTER_REQUIRE_ARRAY
                    )          
                          
            
	);
	$clientData = filter_input_array(INPUT_POST, $definitions);
        $clientDetail = new ApiBusinessClient();
        $clientGet = $clientDetail->getBillingDetails($clientID);
        
        
//        if(isset($_POST["unknown"]) == 1){
//         $smarty->assign("checkboxvalue", $_POST["unknown"]);
//}
//        $firephp->log($_POST);
        
//        $firephp->log($_POST);
//        
//        error_log($clientData);
	foreach($clientData as $key=>$value)
		if($value===null)
			unset($clientData[$key]);
	if(!$clientData){
		$service->setStatus(false);
		$service->summarise('No data fields');
		$service->deliver();
	}
        
//	if($clientData['companyName']=='')
//		$errorFields['companyName']='Company name should not be empty!';
//	if($clientData['billsms']=='')
//		$errorFields['billsms']='billsms should not be empty!';
//	if(($clientData['subIdBillNo']!='') && !filter_var($clientData['contactEmail'], FILTER_VALIDATE_EMAIL))
//		$errorFields['subIdBillNo']='Invalid email address!';
//	if(($clientData['companyUrl']!='') && !filter_var($clientData['companyUrl'], FILTER_VALIDATE_URL))
//		$errorFields['companyUrl']='Invalid company URL!';
	
	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
        
        }if(($clientGet['billsms'] != '') || ($clientGet['subIdBillNo'] != '') || ($clientGet['errorCode'] != '') || 
                ($clientGet['unknown'] != '') || ($clientGet['showDelivered'] != '') || 
                ($clientGet['pending'] != '') || ($clientGet['undelivered'] != '') ||
                ($clientGet['deliveredDesc'] != '') || ($clientGet['totalSms'] != '') ||
                ($clientGet['showProvider'] != '')){
                $clientModel = new ApiBusinessClient();
		$clientModel->updateBilling($clientID, $clientData);
		$service->setStatus(true);
		$service->summarise('Billing data successfully updated');
		$service->deliver();
                    
	}else{
		$clientModel = new ApiBusinessClient();
		$clientModel->insertBilling($clientID, $clientData);
                error_log("insert baru");
		$service->setStatus(true);
		$service->summarise('Billing data successfully inserted');
		$service->deliver();
	}
} catch (Exception $e) {
	$logger->error("$e");
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
}