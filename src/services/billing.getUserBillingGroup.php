<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiReport.php';
$apiReport = new ApiReport();

if ($_POST['type'] === $apiReport::BILLING_TIERING_BASE)
{
	$user          		= $apiReport->getUserDetail($_POST['userID']);
	$userBillingGroup   = $apiReport->getTieringGroupUserList($user['BILLING_TIERING_GROUP_ID']);
}
else
{
	$userBillingGroup = $apiReport->getUserBillingGroup($_POST['userID']);
}

die(json_encode($userBillingGroup));

