<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiCountry.php';

$page = SmsApiAdmin::getTemplate();
SmsApiAdmin::loadConfig('client');
$clientCfg = SmsApiAdmin::getConfig('client');

try {
	$countryModel = new ApiCountry();
	$country = $countryModel->getAll();
	$page->assign('countries', $country);
	$page->assign('defaultCountryCode', $clientCfg['defaultCountry']);
	$page->display('client.regForm.tpl');
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());	
}