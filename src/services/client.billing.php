<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

/**
 * @author Fathir Wafda
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiCountry.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';
require_once '../lib/FirePHPCore/FirePHP.class.php';
$firephp = FirePHP::getInstance(true);
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
        $page->assign('billing', $clientModel->getBillingDetails($clientID));
	$page->display('client.billingForm.tpl');
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}