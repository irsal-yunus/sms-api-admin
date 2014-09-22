<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$username = CommandLine::getArgument('user');
$newPassword = CommandLine::getArgument('set');

if(false === filter_var($username, FILTER_SANITIZE_STRING))
	SmsApiAdminUtility::forceShutDown('Invalid username');
if(($newPassword===null) || ($newPassword===''))
	SmsApiAdminUtility::forceShutDown('Empty password!');

try{
	if($needConfirmation){
		SmsApiAdminUtility::writeLn("Change password of admin <$username> to <$newPassword> ?");
		$confirmed = SmsApiAdminUtility::prompt("Confirm", 1, array('y','n','Y','N'));
		if(strtolower($confirmed)=='n'){
			SmsApiAdminUtility::writeLn('Operation was aborted!');
			exit;
		}
		SmsApiAdminUtility::writeLn('Changing password...');
	}
	$encPassword = sha1($newPassword);
	$db = SmsApiAdminUtility::connectDB();
	$query = 'update ADMIN set ADMIN_PASSWORD=:password where ADMIN_USERNAME=:username';
	$updateStmt = $db->prepare($query);
	$updateStmt->bindValue(':username', $username, PDO::PARAM_STR);
	$updateStmt->bindValue(':password', $encPassword, PDO::PARAM_STR);
	$updateStmt->execute();
	if($updateStmt->rowCount()){
		SmsApiAdminUtility::writeLn("Changed password of admin user <$username>");
	}else{
		SmsApiAdminUtility::writeLn("Failed changing password for admin user <$username>");
	}
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}

