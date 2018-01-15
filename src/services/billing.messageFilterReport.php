<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__DIR__).'/init.d/init.php';
require_once dirname(__DIR__).'/configs/config.php';
require_once dirname(__DIR__).'/classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once dirname(__DIR__).'/classes/PHPExcel.php';
require_once dirname(__DIR__).  '/lib/model/ApiMessageFilterReport.php';

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

$log = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
if(!empty($_FILES) && !empty($_POST['user'])){
   
    $msgContent = [];
    $inputFile = $_FILES['file']['tmp_name'];
    $userAPI = $_POST['user'];

    /**
     * Check if billing report for input user is exist
     */
    $reportDate = explode('-', $_POST['date']);
    $month = $reportDate[0];
    $year = $reportDate[1];
    $apiModel = new ApiMessageFilterReport($month, $year, $userAPI);
    if (!$apiModel->isReportExist()) {
        echo 404;
    } else {

        try {

            /**
             * Update manifest to add current file that being process
             */
            $apiModel->updateManifest(false);
            
            /**
             * Read Message Content File
             */
            $contentReader = ReaderFactory::create(Type::XLSX);
            $contentReader->open($inputFile);
            $contentFilter = [];
            
            foreach ($contentReader->getSheetIterator() as $contentIndex => $contentSheet) {
                foreach ($contentSheet->getRowIterator() as $contentRowIdx => $contentRow) {
                    if ($contentRowIdx != 1 && !in_array("", $contentRow)) {
                        
                        if(array_key_exists($contentRow[1], $contentFilter)){
                            $values = $contentFilter[$contentRow[1]];
                            $values[] = $contentRow[0];
                            
                            $contentFilter[$contentRow[1]] = $values;
                        } else {
                            $contentFilter[$contentRow[1]] = [$contentRow[0]];
                        }
                       
                    }
                }
            }
            
            $log->info($contentFilter);
            $script =  'generateMessageFilterReport.php';

            $argument = base64_encode(json_encode((object) compact('month','year','userAPI', 'contentFilter')));
            $com = 'php ' . $script . ' "' . $argument . '" > /dev/null &';
            exec($com);
            
            echo 200;
        } catch (Throwable $e) {
            echo 500;
            $log->error("Failed to generate report ".$e."");
        }
    }
} else if(!empty($_POST['action']) && $_POST['action'] == 'getManifest') {
    $apiModel = new ApiMessageFilterReport();
    echo json_encode($apiModel->getManifest());
} else {
    $page = SmsApiAdmin::getTemplate();
    $page->assign('siteTitle', SmsApiAdmin::getConfigValue('app', 'siteTitle'));
    $page->display('billing.messageFilterReport.tpl');
}
