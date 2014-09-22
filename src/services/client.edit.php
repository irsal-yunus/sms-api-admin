<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiCountry.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

$page = SmsApiAdmin::getTemplate();
try {
	$clientID = filter_input(INPUT_POST, 'clientID', FILTER_VALIDATE_INT);
	if(empty($clientID))
		SmsApiAdmin::returnError("Invalid clientID($clientID) !");
	$countryModel = new ApiCountry();
	$clientModel = new ApiBusinessClient();
	$country = $countryModel->getAll();
	$page->assign('countries', $country);
	$page->assign('client', $clientModel->getDetails($clientID));
	$page->display('client.editForm.tpl');
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}