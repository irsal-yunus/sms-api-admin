<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'init.d/init.php';
require_once 'configs/config.php';
require_once 'classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once 'classes/PHPExcel.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiMessageContentBasedReport.php';

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

$log = Logger::getRootLogger();
$page = SmsApiAdmin::getTemplate();
$page->assign('siteTitle', SmsApiAdmin::getConfigValue('app', 'siteTitle'));

$login = SmsApiAdmin::getLoginManager();
if ($login->checkIsGuest()) {
    $isLogin = false;
    $welcomeName = 'Guest';
    $page->assign('isLogin', false);
} else {
    $page->assign('isLogin', true);
    $page->assign('welcomeName', $login->getUser()->getDisplayName());
}

if (!empty($_FILES) && !empty($_REQUEST['user'])) {
    $msgContent = [];
    $msgContentFormat = ['CONTENT', 'DEPARTMENT'];
    $inputFile = $_FILES['msgContentFile']['tmp_name'];
    $userAPI = $_REQUEST['user'][0];

    /**
     * Check if billing report for input user is exist
     */
    $apiModel = new ApiMessageContentBasedReport($userAPI);
    if (!$apiModel->isReportExist()) {
        $page->assign('status', 404);
        $page->assign('message', 'Billing report for user ' . $userAPI . ' doesn\'t exist!');
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

            foreach ($contentReader->getSheetIterator() as $contentIndex => $contentSheet) {
                foreach ($contentSheet->getRowIterator() as $contentRowIdx => $contentRow) {
                    if ($contentRowIdx != 1 && !in_array("", $contentRow)) {
                        $msgContent[] = array_combine($msgContentFormat, $contentRow);
                    }
                }
            }
            $script = SMSAPIADMIN_SERVICE_DIR . 'generateMessageContentReport.php';

            $argument = base64_encode(json_encode((object) compact('userAPI', 'msgContent')));
            $com = 'php ' . $script . ' "' . $argument . '" > /dev/null &';
            exec($com);

            $page->assign('status', 200);
            $page->assign('message', 'Report is being generated. Please refresh this page to see the progress! ');
        } catch (Throwable $e) {
            $log->error("Failed to generate report ".$e."");
        }
    }
}
$apiModel = new ApiMessageContentBasedReport();
$allFiles = $apiModel->getManifest();

$page->assign('reportFiles', $allFiles);
$page->display('billing.messageContentReport.tpl');
