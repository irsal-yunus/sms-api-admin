<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once dirname(dirname(dirname(__FILE__))).'/init.d/init.utils.php';

SmsApiAdminUtility::init();
SmsApiAdminUtility::writeLn('Starting update of table USER...');



try{
	$db = SmsApiAdminUtility::connectDB();
	SmsApiAdminUtility::writeLnAndLog("Reading used admin names...");
	$existingAdmin=array();
	$existingAdminTableFormat = '%-32s|%-3s';
	$existingAdminTableLine = '--------------------------------|---';
	$readCreatorQuery = "select distinct(CREATED_BY) from USER";
	$readUpdaterQuery = "select distinct(UPDATED_BY) from USER where UPDATED_BY is not null";
	$registerAdminQuery = 'insert into ADMIN(ADMIN_USERNAME, ADMIN_PASSWORD, ADMIN_DISPLAYNAME, LOGIN_ENABLED)
				values (:username, :password, :displayName, 0)';
	$updateCreatorQuery = 'update USER set CREATED_BY=:newCreator where CREATED_BY=:oldCreator';
	$updateUpdaterQuery = 'update USER set UPDATED_BY=:oldUpdater where UPDATED_BY=:oldUpdater and UPDATED_BY is not null';

	SmsApiAdminUtility::writeLnAndLog("Reading CREATED_BY values of table USER...");
	$creatorResult = $db->query($readCreatorQuery);
	if($creatorResult->rowCount()){
		$creatorResult->setFetchMode(PDO::FETCH_NUM);
		while($data = $creatorResult->fetch()){
			$existingAdmin[$data[0]] = 0;
		}
	}
	unset($creatorResult);
	SmsApiAdminUtility::writeLnAndLog("Freeing result...");

	SmsApiAdminUtility::writeLnAndLog("Reading UPDATED_BY values of table USER...");
	$updaterResult = $db->query($readUpdaterQuery);
	if($updaterResult->rowCount()){
		$updaterResult->setFetchMode(PDO::FETCH_NUM);
		while($data = $updaterResult->fetch()){
			if(!isset($existingAdmin[$adminName]))
				$existingAdmin[$data[0]] = 0;
		}
	}	
	unset($updaterResult);
	SmsApiAdminUtility::writeLnAndLog("Freeing result...");

	$existingAdminCount = count($existingAdmin);
	SmsApiAdminUtility::writeLnAndLog("Found <$existingAdminCount> admin...");	
	if($existingAdminCount){
		SmsApiAdminUtility::getLogger()->info(array_keys($existingAdmin));
		SmsApiAdminUtility::writeLn('--------------------------------');
		SmsApiAdminUtility::writeLn('   Found Creators & Updaters');
		SmsApiAdminUtility::writeLn('--------------------------------');
		SmsApiAdminUtility::writeArrayTable($existingAdmin, true, '%s');
		SmsApiAdminUtility::writeLn('--------------------------------');
		SmsApiAdminUtility::writeLn();
	}

	SmsApiAdminUtility::writeLnAndLog("Preparing update statements...");
	$registerAdminStmt = $db->prepare($registerAdminQuery);
	$updateCreatorStmt = $db->prepare($updateCreatorQuery);
	$updateUpdaterStmt = $db->prepare($updateUpdaterQuery);

	SmsApiAdminUtility::writeLnAndLog("Starting transactions...");
	$db->beginTransaction();
	try{
		SmsApiAdminUtility::writeLnAndLog("Registering admin users...");
		foreach($existingAdmin as $adminName=>$adminID){
			$registerAdminStmt->bindValue(':username', $adminName, PDO::PARAM_STR);
			$registerAdminStmt->bindValue(':password', sha1(uniqid()), PDO::PARAM_STR);
			$registerAdminStmt->bindValue(':displayName', $adminName, PDO::PARAM_STR);
			$registerAdminStmt->execute();
			$existingAdmin[$adminName] = $db->lastInsertId();
			if(!$registerAdminStmt->rowCount()){
				throw new Exception("Failed creating admin user <$adminName>");
			}
			SmsApiAdminUtility::writeLnAndLog("Created admin user <$adminName>, ID={$existingAdmin[$adminName]}");
		}
		SmsApiAdminUtility::writeLnAndLog("Administrators registered...");
		SmsApiAdminUtility::getLogger()->info($existingAdmin);
		SmsApiAdminUtility::writeLn($existingAdminTableLine);
		SmsApiAdminUtility::writeLnFormatted($existingAdminTableFormat, array('Name', 'ID'));
		SmsApiAdminUtility::writeLn($existingAdminTableLine);
		SmsApiAdminUtility::writeArrayMap($existingAdmin, $existingAdminTableFormat);
		SmsApiAdminUtility::writeLn($existingAdminTableLine);
		SmsApiAdminUtility::writeLn();
		
		SmsApiAdminUtility::writeLnAndLog("Updating API users' CREATED_BY values...");
		foreach($existingAdmin as $adminName=>$adminID){
			$updateCreatorStmt->bindValue(':oldCreator', $adminName, PDO::PARAM_STR);
			$updateCreatorStmt->bindValue(':newCreator', $adminID, PDO::PARAM_INT);
			$updateCreatorStmt->execute();
			if(!$updateCreatorStmt->rowCount()){
				throw new Exception("Failed updating user's CREATED_BY where CREATED_BY=<$adminName>");
			}
		}
		SmsApiAdminUtility::writeLnAndLog("CREATED_BY was updated...");

		SmsApiAdminUtility::writeLnAndLog("Updating API users' UPDATED_BY values...");
		foreach($existingAdmin as $adminName=>$adminID){
			$updateUpdaterStmt->bindValue(':oldUpdater', $adminName, PDO::PARAM_STR);
			$updateUpdaterStmt->bindValue(':newUpdater', $adminID, PDO::PARAM_INT);
			$updateUpdaterStmt->execute();
			if(!$updateCreatorStmt->rowCount()){
				throw new Exception("Failed updating user's UPDATED_BY where UPDATED_BY=<$adminName>");
			}
		}
		SmsApiAdminUtility::writeLnAndLog("UPDATED_BY was updated...");

		$answer = SmsApiAdminUtility::prompt("Commit all changes", 1, array('y','n','Y','N'));
		if(strtolower($answer)=='n'){
			SmsApiAdminUtility::writeLnAndLog("Canceling transactions...");
			$db->rollBack();
		}else{
			SmsApiAdminUtility::writeLnAndLog("Comitting changes...");
			$db->commit();
			SmsApiAdminUtility::writeLnAndLog("USER table was updated successfully");
		}
	} catch(Exception $e){
		SmsApiAdminUtility::getLogger()->error("$e");
		SmsApiAdminUtility::writeLn("ERROR: ".$e->getMessage());
		SmsApiAdminUtility::writeLnAndLog("Rolling back transaction...");
		$db->rollBack();
		SmsApiAdminUtility::writeLnAndLog("Transaction has been rolled back");
		SmsApiAdminUtility::writeLn();
		throw new Exception("An error has been occured while registering adminstrators. All transactions have been cancelled!");
	}
	SmsApiAdminUtility::writeLnAndLog("DONE!");
} catch(Exception $e){
	SmsApiAdminUtility::getLogger()->error("$e");
	SmsApiAdminUtility::forceShutDown($e->getMessage());
}