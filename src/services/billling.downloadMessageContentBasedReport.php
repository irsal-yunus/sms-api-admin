<?php


require_once dirname(__DIR__).'/init.d/init.php';
require_once dirname(__DIR__).'/lib/model/ApiMessageContentBasedReport.php';

$log = Logger::getRootLogger();
try {
    
    $report   = $_GET['report'];
    $check    = isset($_GET['check']);
   
    $api = new ApiMessageContentBasedReport();
    
    if($check) {
        echo $api->isReportExist($report)
                ? 200 
                : 404;
    }
    else {
        if($api->isReportExist($report)) {
            $api->downloadReport($report);
        }
        else {
            echo 404;
        }
    }
    
} catch (Throwable $e) {
    $logger->error("generateReportUser error: ".$e->getMessage());
    SmsApiAdmin::returnError($e->getMessage());
}
