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
$userBillingGroup = $apiReport->getUserBillingGroup($_POST['userID']);
die(json_encode($userBillingGroup));
