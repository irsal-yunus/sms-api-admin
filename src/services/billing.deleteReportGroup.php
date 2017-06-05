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

    if(isset($_POST['reportGroupID']) ){
        $reportGroupID = $_POST['reportGroupID'];
  
        $apiReport->deleteReportGroup($reportGroupID);
        
        $updateUserClause = [
            'column'        => 'BILLING_REPORT_GROUP_ID',
            'value'         => 'NULL',
            'whereClause'   => ' BILLING_REPORT_GROUP_ID = '.$reportGroupID.'',
        ];
        $apiReport->updateUser($updateUserClause);
        header("location: ./billing.view.php");
    }
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}