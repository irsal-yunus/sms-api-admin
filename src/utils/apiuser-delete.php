<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
$apiUserName = CommandLine::getArgument('user');
$apiUserClientID = CommandLine::getArgument('client');
$backupIsDisabled = CommandLine::hasArgument('no-backup');
$needConfirmation = CommandLine::getAsBoolean('confirm', true);
$backupFile = CommandLine::getArgument('backup-file');
$isSimulation = CommandLine::getAsBoolean('test');

if(CommandLine::getAsBoolean('delete-all')){
	$includeReplySms = true;
	$includeMessageStatus = true;
	$includeSmsDispatcher = true;
}else{
	$includeReplySms = CommandLine::getAsBoolean('delete-replysms', false);
	$includeSmsDispatcher = CommandLine::getAsBoolean('delete-smsdispatcher', false);
	$includeMessageStatus = $includeSmsDispatcher? CommandLine::getAsBoolean('delete-messagestatus', false) : false;
}


if($backupIsDisabled){
	if(CommandLine::hasArgument('--backup-file')){
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
		'USER_IP'=>'USER_IP.csv',
		'REPLY_BLACKLIST'=>'REPLY_BLACKLIST.csv',
		'SENDER'=>'SENDER.csv',
		'REPLY_SMS'=>'REPLY_SMS.csv',
		'VIRTUAL_NUMBER'=>'VIRTUAL_NUMBER.csv',
		'CREDIT_TRANSACTION'=>'CREDIT_TRANSACTION.csv',
		'USER_MESSAGE_STATUS'=>'USER_MESSAGE_STATUS.csv',
		'SMS_DISPATCHER_SERVER'=>'SMS_DISPATCHER_SERVER.csv',
		'USER'=>'USER.csv'
	);
}

if(false === filter_var($apiUserName, FILTER_SANITIZE_STRING))
	SmsApiAdminUtility::forceShutDown("Invalid username: $apiUserName");
if(trim($apiUserName) == '')
	SmsApiAdminUtility::forceShutDown("Invalid username: $apiUserName");
if(false === filter_var($apiUserClientID, FILTER_VALIDATE_INT))
	SmsApiAdminUtility::forceShutDown("Invalid client ID: $apiUserClientID");

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
				where u.USER_NAME=:username and u.CLIENT_ID=:clientID';
	$readStmt = $db->prepare($readQuery);
	$readStmt->bindValue(':username', $apiUserName, PDO::PARAM_STR);
	$readStmt->bindValue(':clientID', $apiUserClientID, PDO::PARAM_INT);
	$readStmt->execute();
	if(!$readStmt->rowCount()){
		throw new Exception("API User <$apiUserName> of client <$apiUserClientID> record was not found!");
	}
	$apiUserDetails = $readStmt->fetch(PDO::FETCH_ASSOC);
	unset($readStmt); //freeing result
	$apiUserClientName = $apiUserDetails['COMPANY_NAME'];
	$apiUserID = $apiUserDetails['USER_ID'];
	$apiUserIDDisplay = "$apiUserID:$apiUserName";
	SmsApiAdminUtility::writeLnAndLog("About to delete API user <$apiUserIDDisplay> of client <$apiUserClientID:$apiUserClientName>...");
	if($needConfirmation){
		$confirmed = SmsApiAdminUtility::prompt("Really want to delete", 1, array('y','n','Y','N'));
		if(strtolower($confirmed)=='n'){
			SmsApiAdminUtility::writeLn('Operation was aborted!');
			exit;
		}
		SmsApiAdminUtility::writeLn('Proceed to delete user...');
	}
	SmsApiAdminUtility::writeLn();

	//Backup queries
	$backupIPQuery = "select * from USER_IP where USER_ID=:userID";
	$backupReplyBlacklistQuery = "select * from REPLY_BLACKLIST where USER_ID=:userID";
	$backupSenderQuery = "select * from SENDER where USER_ID=:userID";
	$backupReplySmsQuery = "select * from REPLY_SMS where VIRTUAL_NUMBER_ID in
							(select VIRTUAL_NUMBER_ID from VIRTUAL_NUMBER where USER_ID=:userID)";
	$backupVirtualNumberQuery = "select * from VIRTUAL_NUMBER where USER_ID=:userID";
	$backupTransactionQuery = "select * from CREDIT_TRANSACTION where USER_ID=:userID";
	$backupSmsDispatcherQuery = "select * from SMS_DISPATCHER_SERVER
								where USER_MESSAGE_STATUS_ID in
									(select USER_MESSAGE_STATUS_ID from USER_MESSAGE_STATUS where USER_ID=:userID)";
	$backupMessageStatusQuery = "select * from USER_MESSAGE_STATUS where USER_ID_NUMBER=:userID";
	$backupUserQuery = "select * from USER where USER_ID=:userID";

	//Delete queries
	$delUserQuery = "delete from USER where USER_ID=:userID and USER_NAME=:username and CLIENT_ID=:clientID";
	$delIPQuery = "delete from USER_IP where USER_ID=:userID";
	$delReplyBlacklistQuery = "delete from REPLY_BLACKLIST where USER_ID=:userID";
	$delSenderQuery = "delete from SENDER where USER_ID=:userID";
	$delReplySmsQuery = "delete from REPLY_SMS where VIRTUAL_NUMBER_ID in
							(select VIRTUAL_NUMBER_ID from VIRTUAL_NUMBER where USER_ID=:userID)";
	$delVirtualNumberQuery = "delete from VIRTUAL_NUMBER where USER_ID=:userID";
	$delTransactionQuery = "delete from CREDIT_TRANSACTION where USER_ID=:userID";
	$delSmsDispatcherQuery = "delete from SMS_DISPATCHER_SERVER
								where USER_MESSAGE_STATUS_ID in
									(select USER_MESSAGE_STATUS_ID from USER_MESSAGE_STATUS where USER_ID=:userID)";
	$delMessageStatusQuery = "delete from USER_MESSAGE_STATUS where USER_ID_NUMBER=:userID";
	$delUserQuery = "delete from USER where USER_ID=:userID and USER_NAME=:username and CLIENT_ID=:clientID";

	SmsApiAdminUtility::writeLnAndLog("Starting transaction...");
	$db->beginTransaction();
	SmsApiAdminUtility::writeLnAndLog("Transaction has been started");
	try{
		//////////User IP
		if(!$backupIsDisabled){
			$backupIPStmt = $db->prepare($backupIPQuery);
			$backupIPStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$backupIPStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup user IP permission(s)...");
			SmsApiAdminUtility::saveRowsToCsv($backupIPStmt, $tempBackupDir.$backupNames['USER_IP']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupIPStmt); //freeing result
		}
		SmsApiAdminUtility::writeLnAndLog("Deleting user IP record(s) of API user <$apiUserIDDisplay>...");
		$delIPStmt = $db->prepare($delIPQuery);
		$delIPStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
		$delIPStmt->execute();
		$affectedRows = $delIPStmt->rowCount();
		SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> user IP record(s) of API user <$apiUserIDDisplay>");
		SmsApiAdminUtility::writeLn();
		unset($delIPStmt); //freeing result

		//////////Reply Blacklist
		if(!$backupIsDisabled){
			$backupReplyBlacklistStmt = $db->prepare($backupReplyBlacklistQuery);
			$backupReplyBlacklistStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$backupReplyBlacklistStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup reply blacklist item(s)...");
			SmsApiAdminUtility::saveRowsToCsv($backupReplyBlacklistStmt, $tempBackupDir.$backupNames['REPLY_BLACKLIST']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupReplyBlacklistStmt); //freeing result
		}
		SmsApiAdminUtility::writeLnAndLog("Deleting reply blacklist record(s) of API user <$apiUserIDDisplay>...");
		$delReplyBlacklistStmt = $db->prepare($delReplyBlacklistQuery);
		$delReplyBlacklistStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
		$delReplyBlacklistStmt->execute();
		$affectedRows = $delReplyBlacklistStmt->rowCount();
		SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> reply blacklist record(s) of API user <$apiUserIDDisplay>");
		SmsApiAdminUtility::writeLn();
		unset($delReplyBlacklistStmt); //freeing result

		//////////Sender
		if(!$backupIsDisabled){
			$backupSenderStmt = $db->prepare($backupSenderQuery);
			$backupSenderStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$backupSenderStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup sender ID(s)...");
			SmsApiAdminUtility::saveRowsToCsv($backupSenderStmt, $tempBackupDir.$backupNames['SENDER']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupSenderStmt); //freeing result
		}
		SmsApiAdminUtility::writeLnAndLog("Deleting sender ID record(s) of API user <$apiUserIDDisplay>...");
		$delSenderStmt = $db->prepare($delSenderQuery);
		$delSenderStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
		$delSenderStmt->execute();
		$affectedRows = $delSenderStmt->rowCount();
		SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> sender record(s) of API user <$apiUserIDDisplay>");
		SmsApiAdminUtility::writeLn();
		unset($delSenderStmt); //freeing result

		//////////Credit Transaction
		if(!$backupIsDisabled){
			$backupTransactionStmt = $db->prepare($backupTransactionQuery);
			$backupTransactionStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$backupTransactionStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup credit transaction(s)...");
			SmsApiAdminUtility::saveRowsToCsv($backupTransactionStmt, $tempBackupDir.$backupNames['CREDIT_TRANSACTION']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupTransactionStmt); //freeing result
		}
		SmsApiAdminUtility::writeLnAndLog("Deleting transaction record(s) of API user <$apiUserIDDisplay>...");
		$delTransactionStmt = $db->prepare($delTransactionQuery);
		$delTransactionStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
		$delTransactionStmt->execute();
		$affectedRows = $delTransactionStmt->rowCount();
		SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> transaction record(s) of API user <$apiUserIDDisplay>");
		SmsApiAdminUtility::writeLn();
		unset($delTransactionStmt); //freeing result

		//////////Reply SMS
		if($includeReplySms){
			if(!$backupIsDisabled){
				$backupReplySmsStmt = $db->prepare($backupReplySmsQuery);
				$backupReplySmsStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
				$backupReplySmsStmt->execute();
				SmsApiAdminUtility::writeLnAndLog("Backup reply SMS(s)...");
				SmsApiAdminUtility::saveRowsToCsv($backupReplySmsStmt, $tempBackupDir.$backupNames['REPLY_SMS']);
				SmsApiAdminUtility::writeLnAndLog("Backup success");
				unset($backupReplySmsStmt); //freeing result
			}
			SmsApiAdminUtility::writeLnAndLog("Deleting reply SMS record(s) of API user <$apiUserIDDisplay>...");
			$delReplySmsStmt = $db->prepare($delReplySmsQuery);
			$delReplySmsStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$delReplySmsStmt->execute();
			$affectedRows = $delReplySmsStmt->rowCount();
			SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> reply SMS record(s) of API user <$apiUserIDDisplay>");
			SmsApiAdminUtility::writeLn();
			unset($delReplySmsStmt); //freeing result
		}

		//////////Virtual Number
		if(!$backupIsDisabled){
			$backupVirtualNumberStmt = $db->prepare($backupVirtualNumberQuery);
			$backupVirtualNumberStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$backupVirtualNumberStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup virtual number(s)...");
			SmsApiAdminUtility::saveRowsToCsv($backupVirtualNumberStmt, $tempBackupDir.$backupNames['VIRTUAL_NUMBER']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupVirtualNumberStmt); //freeing result
		}
		SmsApiAdminUtility::writeLnAndLog("Deleting virtual number record(s) of API user <$apiUserIDDisplay>...");
		$delVirtualNumberStmt = $db->prepare($delVirtualNumberQuery);
		$delVirtualNumberStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
		$delVirtualNumberStmt->execute();
		$affectedRows = $delVirtualNumberStmt->rowCount();
		SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> virtual number record(s) of API user <$apiUserIDDisplay>");
		SmsApiAdminUtility::writeLn();
		unset($delVirtualNumberStmt); //freeing result

		if($includeSmsDispatcher){
			//////////SMS Dispatcher
			if(!$backupIsDisabled){
				$backupSmsDispatcherStmt = $db->prepare($backupSmsDispatcherQuery);
				$backupSmsDispatcherStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
				$backupSmsDispatcherStmt->execute();
				SmsApiAdminUtility::writeLnAndLog("Backup SMS dispatcher(s)...");
				SmsApiAdminUtility::saveRowsToCsv($backupSmsDispatcherStmt, $tempBackupDir.$backupNames['SMS_DISPATCHER_SERVER']);
				SmsApiAdminUtility::writeLnAndLog("Backup success");
				unset($backupSmsDispatcherStmt); //freeing result
			}
			SmsApiAdminUtility::writeLnAndLog("Deleting reply SMS dispatcher server record(s) of API user <$apiUserIDDisplay>...");
			$delSmsDispatcherStmt = $db->prepare($delSmsDispatcherQuery);
			$delSmsDispatcherStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$delSmsDispatcherStmt->execute();
			$affectedRows = $delSmsDispatcherStmt->rowCount();
			SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> SMS dispatcher server record(s)  of API user <$apiUserIDDisplay>");
			SmsApiAdminUtility::writeLn();
			unset($delSmsDispatcherStmt); //freeing result

			//////////User Message Status
			if($includeMessageStatus){
				if(!$backupIsDisabled){
					$backupMessageStatusStmt = $db->prepare($backupMessageStatusQuery);
					$backupMessageStatusStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
					$backupMessageStatusStmt->execute();
					SmsApiAdminUtility::writeLnAndLog("Backup message status(es)...");
					SmsApiAdminUtility::saveRowsToCsv($backupMessageStatusStmt, $tempBackupDir.$backupNames['SMS_DISPATCHER_SERVER']);
					SmsApiAdminUtility::writeLnAndLog("Backup success");
					unset($backupMessageStatusStmt); //freeing result
				}
				SmsApiAdminUtility::writeLnAndLog("Deleting reply user message status record(s) of API user <$apiUserIDDisplay>...");
				$delMessageStatusStmt = $db->prepare($delMessageStatusQuery);
				$delMessageStatusStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
				$delMessageStatusStmt->execute();
				$affectedRows = $delMessageStatusStmt->rowCount();
				SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> user message status record(s) of API user <$apiUserIDDisplay>");
				SmsApiAdminUtility::writeLn();
				unset($delMessageStatusStmt); //freeing result
			}
		}


		//User
		if(!$backupIsDisabled){
			$backupUserStmt = $db->prepare($backupUserQuery);
			$backupUserStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
			$backupUserStmt->execute();
			SmsApiAdminUtility::writeLnAndLog("Backup user record...");
			SmsApiAdminUtility::saveRowsToCsv($backupUserStmt, $tempBackupDir.$backupNames['SMS_DISPATCHER_SERVER']);
			SmsApiAdminUtility::writeLnAndLog("Backup success");
			unset($backupUserStmt); //freeing result
		}
		$delUserStmt = $db->prepare($delUserQuery);
		$delUserStmt->bindValue(':userID', $apiUserID, PDO::PARAM_INT);
		$delUserStmt->bindValue(':username', $apiUserName, PDO::PARAM_STR);
		$delUserStmt->bindValue(':clientID', $apiUserClientID, PDO::PARAM_INT);
		$delUserStmt->execute();
		$affectedRows = $delUserStmt->rowCount();
		SmsApiAdminUtility::writeLnAndLog("Deleted <$affectedRows> record(s)  of API user <$apiUserIDDisplay>");
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
		unset($backupUserStmt); //freeing result
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