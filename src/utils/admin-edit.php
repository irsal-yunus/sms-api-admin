<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$adminOldName = CommandLine::getArgument('user');
$isSimulation = CommandLine::getAsBoolean('test');
$changeAll = CommandLine::getAsBoolean('change-all');
if($changeAll){
	$changeName = true;
	$changeDisplayedName = true;
	$changeLogin = true;
}else{
	$changeName = CommandLine::getAsBoolean('change-name');
	$changeDisplayedName = CommandLine::getAsBoolean('change-displayname');
	$changeLogin = CommandLine::getAsBoolean('change-login');
}

if(!$changeName && !$changeDisplayedName && !$changeLogin){
	SmsApiAdminUtility::forceShutDown('Nothing to change!');
}

if($adminOldName == SMSAPIADMIN_SYSTEM_USER_NAME){
	SmsApiAdminUtility::writeLnAndLog('Warning: System user <'.SMSAPIADMIN_SYSTEM_USER_NAME.'> can not be activated and its name can not be changed');
	$changeName = false;
	$changeLogin = false;
}

try{
	$db = SmsApiAdminUtility::connectDB();
	$readQuery = "select ADMIN_ID, ADMIN_USERNAME, ADMIN_DISPLAYNAME, if(LOGIN_ENABLED=1, '<TRUE>', '<FALSE>') as LOGIN_ENABLED
					from ADMIN where ADMIN_USERNAME=:username";
	$updateNameQuery = 'update ADMIN set ADMIN_USERNAME=:adminNewName where ADMIN_ID=:adminID';
	$updateDisplayNameQuery = 'update ADMIN set ADMIN_DISPLAYNAME=:adminDisplayName where ADMIN_ID=:adminID';
	$deactivateAdminQuery = "update ADMIN set LOGIN_ENABLED=b'0' where ADMIN_ID=:adminID";
	$activateAdminQuery = "update ADMIN set LOGIN_ENABLED=b'1' where ADMIN_ID=:adminID";


	$readStmt = $db->prepare($readQuery);
	$readStmt->bindValue(':username', $adminOldName, PDO::PARAM_STR);
	$readStmt->execute();
	if($readStmt->rowCount()){
		$details = $readStmt->fetch(PDO::FETCH_ASSOC);
		unset($details['ADMIN_PASSWORD']);
		SmsApiAdminUtility::writeLn();		
		SmsApiAdminUtility::writeLn("ADMIN <$adminOldName> DETAILS");
		SmsApiAdminUtility::writeLn('--------------------------------------------------------------------');
		SmsApiAdminUtility::writeArrayMap($details, "%-20s:%-50s");
		SmsApiAdminUtility::writeLn('--------------------------------------------------------------------');
		SmsApiAdminUtility::writeLn();
		$adminID = $details['ADMIN_ID'];
	}else{
		throw new Exception("Admin user <$adminOldName> does not exist!");
	}

	$db->beginTransaction();
	try{

		$validAnswer = false;
		if($changeName){
			SmsApiAdminUtility::writeLn("Change the login name from <{$details['ADMIN_USERNAME']}>?");
			while($adminNewName = SmsApiAdminUtility::prompt("Enter new login name: ", 16)){
				$adminNewName = trim($adminNewName);
				$validAnswer = filter_var($adminNewName,
											FILTER_VALIDATE_REGEXP,
											array('options'=>array('regexp'=>'/^([A-za-z0-9_]{3,16})$/')));
				if($validAnswer){
					SmsApiAdminUtility::writeLn("Changing admin login name to <{$adminNewName}>");
					break;
				}else{
					SmsApiAdminUtility::writeLn("Invalid login name <{$adminNewName}>");
				}
			}
			$updateNameStmt = $db->prepare($updateNameQuery);
			$updateNameStmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
			$updateNameStmt->bindValue(':adminNewName', $adminNewName, PDO::PARAM_STR);
			$updateNameStmt->execute();
			SmsApiAdminUtility::writeLn("Admin login name changed to <{$adminNewName}>");
			SmsApiAdminUtility::writeLn();
		}

		$validAnswer = false;
		if($changeDisplayedName){
			SmsApiAdminUtility::writeLn("Change the displayed name from <{$details['ADMIN_DISPLAYNAME']}>?");
			while($adminDisplayedName = SmsApiAdminUtility::prompt("Enter new displayed name: ", 32)){
				$adminDisplayedName = filter_var($adminDisplayedName,
											FILTER_SANITIZE_STRING,
											array('flags'=>FILTER_FLAG_STRIP_LOW));
				if($adminDisplayedName === false){
					SmsApiAdminUtility::writeLn("Invalid name <{$adminDisplayedName}>");
				}elseif(trim($adminDisplayedName) === ''){
					SmsApiAdminUtility::writeLn("Empty name <{$adminDisplayedName}>");
				}else{
					SmsApiAdminUtility::writeLn("Changing admin displayed name to <{$adminDisplayedName}>");
					break;
				}
			}
			$updateDisplayNameStmt = $db->prepare($updateDisplayNameQuery);
			$updateDisplayNameStmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
			$updateDisplayNameStmt->bindValue(':adminDisplayName', $adminDisplayedName, PDO::PARAM_STR);
			$updateDisplayNameStmt->execute();
			SmsApiAdminUtility::writeLn("Admin displayed name changed to <{$adminDisplayedName}>");
			SmsApiAdminUtility::writeLn();
		}

		if($changeLogin){
			$answer = SmsApiAdminUtility::prompt("Activate admin user?", 1, array('y','n','Y','N'));
			if(strtolower($answer)=='n'){
				$deactivateAdminStmt = $db->prepare($deactivateAdminQuery);
				$deactivateAdminStmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
				$deactivateAdminStmt->execute();
				SmsApiAdminUtility::writeLn("Admin login is now BANNED");
			}else{
				$activateAdminStmt = $db->prepare($activateAdminQuery);
				$activateAdminStmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
				$activateAdminStmt->execute();
				SmsApiAdminUtility::writeLn("Admin login is now ENABLED");
			}
			SmsApiAdminUtility::writeLn();
		}


		if($isSimulation){
			SmsApiAdminUtility::writeLnAndLog("Simulation mode detected, rolling back transactions...");
			$db->rollBack();
			SmsApiAdminUtility::writeLnAndLog("Transactions have been rolled back");
		}else{
			SmsApiAdminUtility::writeLnAndLog("Closing transactions...");
			$db->commit();
			SmsApiAdminUtility::writeLnAndLog("Transactions have been commited");
		}
	} catch(Exception $e){
		SmsApiAdminUtility::getLogger()->error("$e");
		SmsApiAdminUtility::writeLn("ERROR: ".$e->getMessage());
		SmsApiAdminUtility::writeLnAndLog("Rolling back transaction...");
		$db->rollBack();
		SmsApiAdminUtility::writeLnAndLog("Transaction has been rolled back");
		SmsApiAdminUtility::writeLn();
		throw new Exception("An error has been occured while running transaction. All transactions have been cancelled!");
	}
	SmsApiAdminUtility::writeLn('DONE!');	
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}

