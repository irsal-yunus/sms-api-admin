<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$logger = Logger::getLogger($app);
$username = CommandLine::getArgument('user');

if(false === filter_var($username, FILTER_SANITIZE_STRING))
	SmsApiAdminUtility::forceShutDown('Invalid username');

try{
	$db = SmsApiAdminUtility::connectDB();
	$query = 'update ADMIN set LOGIN_ENABLED=0 where ADMIN_USERNAME=:username';
	$statement = $db->prepare($query);
	$statement->bindValue(':username', $username, PDO::PARAM_STR);
	$statement->execute();
	if($statement->rowCount()){
		SmsApiAdminUtility::writeLn("Successfully disabled admin user: $username");
	}else{
		SmsApiAdminUtility::writeLn("Nothing is updated for $username");
	}
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}

