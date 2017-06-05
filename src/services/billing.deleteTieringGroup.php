<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiReport.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();

try {
    $apiReport = new ApiReport();

    if(isset($_POST['tieringGroupID']) ){
        $tieringGroupID = $_POST['tieringGroupID'];
  
        $apiReport->deleteTieringGroup($tieringGroupID);
        
        $updateUserClause = [
            'column'        => 'BILLING_TIERING_GROUP_ID',
            'value'         => 'NULL',
            'whereClause'   => ' BILLING_TIERING_GROUP_ID = '.$tieringGroupID.'',
        ];
        $apiReport->updateUser($updateUserClause);
        header("location: ./billing.view.php");
    }
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}