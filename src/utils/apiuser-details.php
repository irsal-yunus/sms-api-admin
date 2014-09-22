<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$username = CommandLine::getArgument('user');

if(false === filter_var($username, FILTER_SANITIZE_STRING))
	SmsApiAdminUtility::forceShutDown("Invalid username: $username");
if($username == '')
	SmsApiAdminUtility::forceShutDown("Invalid username: $username");

try{
	$db = SmsApiAdminUtility::connectDB();
	$query = 'select * from USER where USER_NAME=:username';
	$statement = $db->prepare($query);
	$statement->bindValue(':username', $username, PDO::PARAM_STR);
	$statement->execute();
	if($statement->rowCount()){
		$details = $statement->fetch(PDO::FETCH_ASSOC);
		SmsApiAdminUtility::writeLn();
		SmsApiAdminUtility::writeLn("USER <$username> DETAILS");
		SmsApiAdminUtility::writeLn('--------------------------------------------------------------------');
		SmsApiAdminUtility::writeArrayMap($details, "%-20s:%-50s");
		SmsApiAdminUtility::writeLn('--------------------------------------------------------------------');
		SmsApiAdminUtility::writeLn();
	}else{
		SmsApiAdminUtility::writeLn("API user <$username> does not exist!");
	}
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}

