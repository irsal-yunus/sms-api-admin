<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$username = CommandLine::getArgument('user');
$clientID = CommandLine::getArgument('client');

if(false === filter_var($username, FILTER_SANITIZE_STRING))
	SmsApiAdminUtility::forceShutDown('Invalid username');
if(false === filter_var($clientID, FILTER_VALIDATE_INT))
	SmsApiAdminUtility::forceShutDown("Invalid client ID: $clientID");

try{
	$db = SmsApiAdminUtility::connectDB();
	$selectQuery = 'select USER_NAME, CLIENT_ID from USER where USER_NAME=:username';
	$updateQuery = 'update USER set CLIENT_ID=:clientID, UPDATED_BY=:updatedBy, UPDATED_DATE=now() where USER_NAME=:username';
	$selectStmt = $db->prepare($selectQuery);
	$selectStmt->bindValue(':username', $username, PDO::PARAM_STR);
	$selectStmt->execute();
	if(!$selectStmt->rowCount()){
		SmsApiAdminUtility::forceShutDown("API User not found: $username");
	}
	$oldDetails = $selectStmt->fetch(PDO::FETCH_ASSOC);
	SmsApiAdminUtility::writeLnAndLog("Updating API user <{$oldDetails['USER_NAME']}>,  change clientID from <{$oldDetails['CLIENT_ID']}> to <$clientID>");
	$updateStmt = $db->prepare($updateQuery);
	$updateStmt->bindValue(':username', $username, PDO::PARAM_STR);
	$updateStmt->bindValue(':clientID', $clientID, PDO::PARAM_STR);
	$updateStmt->bindValue(':updatedBy', SmsApiAdminUtility::APP_USER_ID, PDO::PARAM_STR);
	$updateStmt->execute();
	if($updateStmt->rowCount()){
		$selectStmt->bindValue(':username', $username, PDO::PARAM_STR);
		$selectStmt->execute();
		if(!$updateStmt->rowCount()){
			throw new RuntimeException("API user <$username> record is missing");
		}
		$newDetails = $selectStmt->fetch(PDO::FETCH_ASSOC);
		SmsApiAdminUtility::writeLnAndLog("API user <{$newDetails['USER_NAME']}> now has clientID <{$newDetails['CLIENT_ID']}>");
	}else{
		SmsApiAdminUtility::writeLn("Nothing is updated for user <$username>");
	}
	SmsApiAdminUtility::writeLn('DONE!');
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}

