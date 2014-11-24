<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
try {
	$errorFields = array();
	$userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	$inquiry = array();
	if(!$userID)
		SmsApiAdmin::returnError('Missing userID in arguments!');

	$urlActive = filter_var($_POST['statusDeliveryActive'], FILTER_VALIDATE_BOOLEAN);
	$inquiry['statusDeliveryActive'] = $urlActive;
	if($urlActive){		
		if($urlActive){
			$deliveryUrl = filter_input(INPUT_POST, 'statusDeliveryUrl', FILTER_VALIDATE_URL, array('flags'=>FILTER_FLAG_SCHEME_REQUIRED));
			$inquiry['statusDeliveryUrl'] = $deliveryUrl;
			if($deliveryUrl === null){
				$errorFields['statusDeliveryUrl']= 'Delivery URL must be specified when status delivery is enabled';
			}elseif($deliveryUrl === false){
				$errorFields['statusDeliveryUrl']= 'Invalid status delivery URL';
			}
		}else{
			$inquiry['statusDeliveryUrl'] = null;
		}
	}
	if(isset($_POST['userName'])){
		$inquiry['userName'] = trim(filter_var($_POST['userName'], FILTER_SANITIZE_STRING, array('flags'=>FILTER_FLAG_STRIP_HIGH| FILTER_FLAG_STRIP_LOW)));
		if($_POST['userName'] == '')
			$errorFields['userName']= 'Username can not be empty';
	}
        $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
        $dateNow= date('Y-m-d H:i:s',$tomorrow);
	if(isset($_POST['expiredDate'])){
		$inquiry['expiredDate'] = trim(filter_var($_POST['expiredDate'], FILTER_SANITIZE_STRING, array('flags'=>FILTER_FLAG_STRIP_HIGH| FILTER_FLAG_STRIP_LOW)));
		if($_POST['expiredDate'] == '')
			$errorFields['expiredDate']= 'Expired date can not be empty';
		else if($_POST['expiredDate'] < $dateNow)
			$errorFields['expiredDate']= 'Expired date must be greater then today';
                
	}
	if(isset($_POST['cobranderID'])){
		$inquiry['cobranderID'] = trim(filter_var($_POST['cobranderID'], FILTER_SANITIZE_STRING, array('flags'=>FILTER_FLAG_STRIP_HIGH| FILTER_FLAG_STRIP_LOW)));
	}	
	$inquiry['replyBlacklistEnabled'] = filter_input(INPUT_POST, 'replyBlacklistEnabled', 
										FILTER_VALIDATE_BOOLEAN )? 1 : 0;
	$inquiry['isPostpaid'] = filter_input(INPUT_POST, 'isPostpaid',
										FILTER_VALIDATE_BOOLEAN )? 1 : 0;
	if($errorFields){
		$service->setStatus(false);
		$service->summarise('Input fields error');
		$service->attachRaw($errorFields);
		$service->deliver();
	}
	$dataManager = new ApiUser();
	$dataManager->update($userID, $inquiry);
	$service->setStatus(true);
	$service->deliver();
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}