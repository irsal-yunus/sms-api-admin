<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();

try{
	$db = SmsApiAdminUtility::connectDB();
	$query = "select
					ADMIN_ID,
					ADMIN_USERNAME,
					if((LOGIN_ENABLED=1), 'Y', 'N') as enabled,
					ADMIN_DISPLAYNAME
				from ADMIN
				order by ADMIN_USERNAME";
	$result = $db->query($query);
	if($result->rowCount()){
		$list = $result->fetchAll(PDO::FETCH_NUM);
		$tableFormat=" %8d | %-16s | %7s | %-32s";
		$tableHeaderFormat=" %-8s | %-16s | %-7s | %-32s";
		$tableLine=  str_repeat('-', 70);
		SmsApiAdminUtility::writeLn();
		SmsApiAdminUtility::writeLn("SMS API V2 ADMINISTRATORS TABLE");
		SmsApiAdminUtility::writeLn($tableLine);
		SmsApiAdminUtility::writeLn(sprintf($tableHeaderFormat, 'ID', 'Username', 'Enabled', 'Display Name'));
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

