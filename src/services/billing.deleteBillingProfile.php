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
    
    //Validate if billingProfileID is set
    if(isset($_POST['billingProfileID']) ){
        $billingProfileID = $_POST['billingProfileID'];
        if(strtolower($_POST['billingType'] == 'operator')){
            $apiReport->deleteBillingProfileOperator($billingProfileID);
        }else{
            $apiReport->deleteBillingProfileTiering($billingProfileID);
        }
        $apiReport->deleteBillingProfile($billingProfileID);
        $updateUserClause = [
            'column'        => 'BILLING_PROFILE_ID',
            'value'         => 'NULL',
            'whereClause'   => ' BILLING_PROFILE_ID = '.$billingProfileID.'',
        ];
        $apiReport->updateUser($updateUserClause);
        header("location: ./billing.view.php");
    }
} catch (Exception $e) {
	Logger::getRootLogger()->error("$e");
	SmsApiAdmin::returnError($e->getMessage());
}