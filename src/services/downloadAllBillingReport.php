<?php

/**
 * @author Basri.Y      
 * 
 * Copyright(c) 2017 1rstWAP. All rights reserved.
 * -----------------------------------------------
 * #18923   2017-05-03  Basri.Y     [Billing] Download All Reports
 */

require_once '../init.d/init.service.php';
require_once '../init.d/init.php';


$logger = Logger::getRootLogger();

try {
    ini_set('max_execution_time', 7200);
    
    $period    = isset($_GET['period']) ? $_GET['period'] : false;
    $check     = isset($_GET['check']);
    $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT .$period.'/';
    $filename  = 'BillingReport_'.$period.'.zip';

    if ($check) {
        echo file_exists($directory.$filename)
                ? 'Exist'
                : 'File Doesn\'t Exist';
    }
    else if( file_exists($directory.$filename) ) { 
        // http headers for zip downloads
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".$filename."\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($directory.$filename));
        ob_end_flush();
        @readfile($directory.$filename);
    }
    else {
        echo 'File Doesn\'t Exist';
    }

    
} catch (Throwable $e) {
    $logger->error("DownloadAllBillingReport error: ".$e->getMessage());
    SmsApiAdmin::returnError($e->getMessage());
}
