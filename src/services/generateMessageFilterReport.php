<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__DIR__).'/init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiMessageFilterReport.php';

$log = Logger::getRootLogger();

/**
 * Create instance of ApiMessageContentBasedReport
 */
isset($argv[1]) ?: exit();


/**
 * Decode argument and load Required Class for unserialize Object
 */
$request = json_decode(base64_decode($argv[1]));
try{
    $apiModel = new ApiMessageFilterReport($request->month, $request->year,$request->userAPI, $request->contentFilter);
    $apiModel->generateReport();
} catch (Throwable $e){
    $log->error("Failed to generate Report" . $e);
}


