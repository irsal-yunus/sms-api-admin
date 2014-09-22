<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';
SmsApiAdminUtility::init();;

$username = trim(CommandLine::getArgument('user'));
$password = CommandLine::getArgument('pass');
$displayName = trim(CommandLine::getArgument('name'));

if(false === filter_var($username,
						FILTER_VALIDATE_REGEXP,
						array('options'=>array('regexp'=>'/^([A-za-z0-9_]{3,16})$/'))))
	SmsApiAdminUtility::forceShutDown("Invalid username: $username");
if(($password==='') || ($password===null))
	SmsApiAdminUtility::forceShutDown('Empty password');
if((false === ($displayName = filter_var($displayName,
						FILTER_SANITIZE_STRING,
						array('flags'=>FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))))
		|| ($displayName===''))
	SmsApiAdminUtility::forceShutDown("Invalid admin name: $displayName");

try{
	$db = SmsApiAdminUtility::connectDB();
	$query = 'insert into ADMIN(ADMIN_USERNAME, ADMIN_PASSWORD, ADMIN_DISPLAYNAME, LOGIN_ENABLED)
				values (:username, :password, :displayName, 1)';
	$statement = $db->prepare($query);
	$statement->bindValue(':username', $username, PDO::PARAM_STR);
	$statement->bindValue(':password', sha1($password), PDO::PARAM_STR);
	$statement->bindValue(':displayName', $displayName, PDO::PARAM_STR);
	$statement->execute();
	if($statement->rowCount()){
		SmsApiAdminUtility::writeLn("Successfully created admin user: $username");
	}else{
		SmsApiAdminUtility::writeLn("Failed creating admin user: $username");
	}
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}

