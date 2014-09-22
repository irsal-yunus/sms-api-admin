<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$creditUserName= CommandLine::hasArgument('user')? CommandLine::getArgument('user') : null;
$creditDateFrom = CommandLine::hasArgument('date-from')? CommandLine::getArgument('date-from') : null;
$creditDateTo = CommandLine::hasArgument('date-to')? CommandLine::getArgument('date-to') : null;
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
		'CREDIT_TRANSACTION'=>'CREDIT_TRANSACTION.csv'
	);
}

$deleteByDateTo = $creditDateTo!==null;
$deleteByDateFrom = $creditDateFrom!==null;
$deleteByUser = $creditUserName!==null;
$deleteAll = !$deleteByUser && !$deleteByDateFrom && !$deleteByDateTo;
if($deleteAll){
	$confirmed = SmsApiAdminUtility::prompt("Really want to delete ALL credit history records", 1, array('y','n','Y','N'));
	if(strtolower($confirmed)=='n'){
		SmsApiAdminUtility::writeLn('Operation was aborted!');
		exit;
	}
	SmsApiAdminUtility::writeLn('Proceed to delete ALL user credit history...');
}elseif($deleteByUser){
	if(false === filter_var($creditUserName, FILTER_SANITIZE_STRING))
		SmsApiAdminUtility::forceShutDown("Invalid username: $creditUserName");
	if(trim($creditUserName) == '')
		SmsApiAdminUtility::forceShutDown("Invalid username: $creditUserName");
}



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
	if($deleteByUser){
		$readQuery = 'select u.USER_ID, u.USER_NAME, u.CLIENT_ID, c.COMPANY_NAME
					from USER as u
						inner join CLIENT as c on (u.CLIENT_ID=c.CLIENT_ID)
					where u.USER_NAME=:username';
		$readStmt = $db->prepare($readQuery);
		$readStmt->bindValue(':username', $creditUserName, PDO::PARAM_STR);
		$readStmt->execute();
		if(!$readStmt->rowCount()){
			throw new Exception("API User <$creditUserName> record was not found!");
		}
		$apiUserDetails = $readStmt->fetch(PDO::FETCH_ASSOC);
		$creditUserID = $apiUserDetails['USER_ID'];
	}

	SmsApiAdminUtility::writeLnAndLog("Checking credit records...");
	$checkQuery = 'select count(*) from CREDIT_TRANSACTION';
	$deleteFiltersDesc = array();
	if(!$deleteAll){
		$deleteFiltersSql = array();
		if($deleteByUser){
			$deleteFiltersSql[] = 'USER_ID=:userID';
			$deleteFiltersDesc[] = "UserID=<$creditUserID>, UserName=<$creditUserName>";
		}
		if($deleteByDateFrom){
			$deleteFiltersSql[] = 'CREATED_DATE>=:fromDate';
			$deleteFiltersDesc[] = "Date From=<$creditDateFrom>";
		}
		if($deleteByDateTo){
			$deleteFiltersSql[] = 'CREATED_DATE<=:toDate';
			$deleteFiltersDesc[] = "Date To=<$creditDateTo>";
		}
		$deleteFiltersSql = ' where '.implode(' and ', $deleteFiltersSql);
		$checkQuery .= $deleteFiltersSql;
	}else{
		$deleteFiltersDesc = 'all history';
	}
	$deleteFiltersDesc = implode(', ', $deleteFiltersDesc);
	$checkStmt = $db->prepare($checkQuery);
	if(!$deleteAll){
		if($deleteByUser){
			$checkStmt->bindValue(':userID', $creditUserID, PDO::PARAM_INT);
		}
		if($deleteByDateFrom){
			$checkStmt->bindValue(':fromDate', $creditDateFrom, PDO::PARAM_STR);
		}
		if($deleteByDateTo){
			$checkStmt->bindValue(':toDate', $creditDateTo, PDO::PARAM_STR);
		}
	}
	$checkStmt->execute();
	$creditCount = $checkStmt->fetchColumn(0);
	if(!$creditCount){
		throw new Exception("No credit history was found for $deleteFiltersDesc");
	}else{
		SmsApiAdminUtility::writeLnAndLog("Found <$creditCount> history record(s)");
	}
	unset($checkStmt);

	SmsApiAdminUtility::writeLnAndLog("About to delete credit history records for $deleteFiltersDesc...");
	if($needConfirmation){
		$confirmed = SmsApiAdminUtility::prompt("Really want to delete", 1, array('y','n','Y','N'));
		if(strtolower($confirmed)=='n'){
			SmsApiAdminUtility::writeLn('Operation was aborted!');
			exit;
		}
		SmsApiAdminUtility::writeLn('Proceed to delete history...');
	}
	SmsApiAdminUtility::writeLn();

	//Backup queries
	$backupHistoryQuery = "select * from CREDIT_TRANSACTION $deleteFiltersSql";

	//Delete queries
	$delHistoryQuery = "delete from CREDIT_TRANSACTION $deleteFiltersSql";
	
	SmsApiAdminUtility::writeLnAndLog("Starting transaction...");
	$db->beginTransaction();
	SmsApiAdminUtility::writeLnAndLog("Transaction has been started");
	try{
		if(!$backupIsDisabled){
			$backupHistoryStmt = $db->prepare($backupHistoryQuery);
			if(!$deleteAll){
				if($deleteByUser){
					$backupHistoryStmt->bindValue(':userID', $creditUserID, PDO::PARAM_INT);
				}
				if($deleteByDateFrom){
					$backupHistoryStmt->bindValue(':fromDate', $creditDateFrom, PDO::PARAM_STR);
				}
				if($deleteByDateTo){
					$backupHistoryStmt->bindValue(':toDate', $creditDateTo, PDO::PARAM_STR);
				}
			}
			$backupHistoryStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup credit transactions data...");
			SmsApiAdminUtility::saveRowsToCsv($backupHistoryStmt, $tempBackupDir.$backupNames['CREDIT_TRANSACTION']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupHistoryStmt);
		}
		
		SmsApiAdminUtility::writeLnAndLog("Deleting sender data...");
		$delHistoryStmt = $db->prepare($delHistoryQuery);
		if(!$deleteAll){
			if($deleteByUser){
				$delHistoryStmt->bindValue(':userID', $creditUserID, PDO::PARAM_INT);
			}
			if($deleteByDateFrom){
				$delHistoryStmt->bindValue(':fromDate', $creditDateFrom, PDO::PARAM_STR);
			}
			if($deleteByDateTo){
				$delHistoryStmt->bindValue(':toDate', $creditDateTo, PDO::PARAM_STR);
			}
		}
		$delHistoryStmt->execute();
		$affectedRows = $delHistoryStmt->rowCount();
		unset($delHistoryStmt);
		
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