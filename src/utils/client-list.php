<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();

try{
	$db = SmsApiAdminUtility::connectDB();
	$query = 'select CLIENT_ID, COMPANY_NAME, CONTACT_NAME from CLIENT order by COMPANY_NAME';
	$result = $db->query($query);
	if($result->rowCount()){
		$list = $result->fetchAll(PDO::FETCH_NUM);
		$tableFormat="%8d | %-30s| %-30s";
		$tableHeaderFormat="%-8s | %-30s| %-30s";
		$tableLine=  str_repeat('-', 73);
		SmsApiAdminUtility::writeLn();
		SmsApiAdminUtility::writeLn("SMS API V2 CLIENTS TABLE");
		SmsApiAdminUtility::writeLn($tableLine);
		SmsApiAdminUtility::writeLn(sprintf($tableHeaderFormat, 'ClientID', 'Company', 'Contact'));
		SmsApiAdminUtility::writeLn($tableLine);
		SmsApiAdminUtility::writeArrayTable($list, false, $tableFormat);
		SmsApiAdminUtility::writeLn($tableLine);
		SmsApiAdminUtility::writeLn();
	}else{
		SmsApiAdminUtility::writeLn("No client record exists!");
	}
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}

