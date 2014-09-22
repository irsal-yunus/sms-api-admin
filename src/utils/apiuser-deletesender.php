<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$apiUserName = CommandLine::getArgument('user');
$apiSenderName = CommandLine::getArgument('sender-name');
$backupIsDisabled = CommandLine::hasArgument('no-backup');
$needConfirmation = CommandLine::getAsBoolean('confirm', true);
$backupFile = CommandLine::getArgument('backup-file');
$isSimulation = CommandLine::getAsBoolean('test');

if($backupIsDisabled){
	if(CommandLine::hasArgument('backup-file')){
		SmsApiAdminUtility::writeLn('Option --backup-file and --no-backup are mutually exclusive');
		SmsApiAdminUtility::showHelp();
		exit;
	}
	$backupNames=array();
}else{
	if($backupFile==''){
		$backupFile = 'backup-data-'.date('Ymd-His').SMSAPIADMIN_ARCHIEVE_DEFAULT_SUFFIX;
	}
	$backupNames = array(
		'SENDER'=>'SENDER.csv'
	);
}

if(false === filter_var($apiUserName, FILTER_SANITIZE_STRING))
	SmsApiAdminUtility::forceShutDown("Invalid username: $apiUserName");
if(trim($apiUserName) == '')
	SmsApiAdminUtility::forceShutDown("Invalid username: $apiUserName");

if(false === filter_var($apiSenderName, FILTER_SANITIZE_STRING))
	SmsApiAdminUtility::forceShutDown("Invalid sender name: $apiSenderName");
if(trim($apiSenderName) == '')
	SmsApiAdminUtility::forceShutDown("Invalid sender name: $apiSenderName");

//create temp dir
SmsApiAdminUtility::writeLnAndLog("Creating temporary directory...");
$tempTry = 0;
while(++$tempTry <=3 ){
	$tempID = uniqid('', true);
	$tempDir = SMSAPIADMIN_UTILS_TMP_DIR.$tempID;
	if(file_exists($tempDir)){
		SmsApiAdminUtility::writeLnAndLog("Temporary directory creation attempt-$tempTry: File $tempDir exists...");
		$tempDir = false;
		continue;
	}elseif(!mkdir($tempDir, 0755)){
		throw new RuntimeException("Could not create temporary directory: $tempDir");
	}else{
		$tempDir.='/';
		SmsApiAdminUtility::writeLnAndLog("Created temporary directory in $tempDir");
		$tempBackupDir = "{$tempDir}backup".date('-Ymd-His');
		SmsApiAdminUtility::writeLnAndLog("Creating temporary backup directory in $tempBackupDir");
		if(!mkdir($tempBackupDir, 0755)){
			throw new Exception('Failed creating backup directory!');
		}else{
			$tempBackupDir.='/';
		}
		SmsApiAdminUtility::writeLnAndLog("Using temporary directory: $tempDir");
		break;
	}
}
if($tempDir===false){
	throw new Exception('Temprorary directory was not created!');
}

SmsApiAdminUtility::writeLn();
try{
	$db = SmsApiAdminUtility::connectDB();
	$readQuery = 'select u.USER_ID, u.USER_NAME, u.CLIENT_ID, c.COMPANY_NAME
				from USER as u
					inner join CLIENT as c on (u.CLIENT_ID=c.CLIENT_ID)
				where u.USER_NAME=:username';
	$readStmt = $db->prepare($readQuery);
	$readStmt->bindValue(':username', $apiUserName, PDO::PARAM_STR);
	$readStmt->execute();
	if(!$readStmt->rowCount()){
		throw new Exception("API User <$apiUserName> record was not found!");
	}
	$apiUserDetails = $readStmt->fetch(PDO::FETCH_ASSOC);
	$apiUserID = $apiUserDetails['USER_ID'];
	
	SmsApiAdminUtility::writeLnAndLog("Checking sender <$apiSenderName> existence...");
	$checkQuery = 'select count(*) from SENDER where USER_ID=:userID and SENDER_NAME=:senderName';
	$checkStmt = $db->prepare($checkQuery);
	$checkStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
	$checkStmt->bindValue(':senderName', $apiSenderName, PDO::PARAM_STR);
	$checkStmt->execute();
	if($checkStmt->fetchColumn(0) < 1){
		throw new Exception("API User <$apiUserName> is not configured with sender <$apiSenderName>!");
	}
	SmsApiAdminUtility::writeLnAndLog("Sender <$apiSenderName> for user <$apiUserName> exists");
	unset($checkStmt);


	SmsApiAdminUtility::writeLnAndLog("About to delete sender <$apiSenderName> from API user <$apiUserName>...");
	if($needConfirmation){
		$confirmed = SmsApiAdminUtility::prompt("Really want to delete", 1, array('y','n','Y','N'));
		if(strtolower($confirmed)=='n'){
			SmsApiAdminUtility::writeLn('Operation was aborted!');
			exit;
		}
		SmsApiAdminUtility::writeLn('Proceed to delete sender...');
	}
	SmsApiAdminUtility::writeLn();

	//Backup queries
	$backupSenderQuery = "select * from SENDER where USER_ID=:userID and SENDER_NAME=:senderName";

	//Delete queries
	$delSenderQuery = "delete from SENDER where USER_ID=:userID and SENDER_NAME=:senderName";
	
	SmsApiAdminUtility::writeLnAndLog("Starting transaction...");
	$db->beginTransaction();
	SmsApiAdminUtility::writeLnAndLog("Transaction has been started");
	try{
		if(!$backupIsDisabled){
			$backupSenderStmt = $db->prepare($backupSenderQuery);
			$backupSenderStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$backupSenderStmt->bindValue(':senderName', $apiSenderName, PDO::PARAM_STR);
			$backupSenderStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup sender data...");
			SmsApiAdminUtility::saveRowsToCsv($backupSenderStmt, $tempBackupDir.$backupNames['SENDER']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupSenderStmt);
		}
		
		SmsApiAdminUtility::writeLnAndLog("Deleting sender data...");
		$delSenderStmt = $db->prepare($delSenderQuery);
		$delSenderStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
		$delSenderStmt->bindValue(':senderName', $apiSenderName, PDO::PARAM_STR);
		$delSenderStmt->execute();
		$affectedRows = $delSenderStmt->rowCount();
		unset($delSenderStmt);
		
		SmsApiAdminUtility::writeLnAndLog("Sender name <$apiSenderName> has been deleted");
		if($isSimulation){
			SmsApiAdminUtility::writeLnAndLog("Simulation mode detected, rolling back transactions...");
			$db->rollBack();
			SmsApiAdminUtility::writeLnAndLog("Transactions have been rolled back");
		}else{
			SmsApiAdminUtility::writeLnAndLog("Closing transactions...");
			$db->commit();
			SmsApiAdminUtility::writeLnAndLog("Transactions have been commited");
		}
		SmsApiAdminUtility::writeLn();
	} catch(Exception $e){
		SmsApiAdminUtility::getLogger()->error("$e");
		SmsApiAdminUtility::writeLn("ERROR: ".$e->getMessage());
		SmsApiAdminUtility::writeLnAndLog("Rolling back transaction...");
		$db->rollBack();
		SmsApiAdminUtility::writeLnAndLog("Transaction has been rolled back");
		SmsApiAdminUtility::writeLn();
		throw new Exception("An error has been occured while running transaction. All transactions have been cancelled!");
	}
	SmsApiAdminUtility::writeLn();

	if(!$backupIsDisabled){
		SmsApiAdminUtility::writeLnAndLog("Archiving backup to <$backupFile> ...");
		SmsApiAdminUtility::compress($tempBackupDir, $backupFile);
		SmsApiAdminUtility::writeLnAndLog("Archived!");
	}	
	SmsApiAdminUtility::writeLnAndLog("Cleaning up temporary files...");
	if($tempDir !== false){
		if(SmsApiAdminUtility::unlink($tempDir)){
			SmsApiAdminUtility::getLogger()->error("Failed removing temporary files");
			SmsApiAdminUtility::writeLn("WARNING: Failed removing temporary files");
		}
	}
	SmsApiAdminUtility::writeLn();

	SmsApiAdminUtility::writeLnAndLog("DONE!");
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}