<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();

    $profileId = filter_input(INPUT_POST, 'profileId', FILTER_VALIDATE_INT);

    if (empty($profileId)) {
        SmsApiAdmin::returnError("Invalid Profile ID ($profileId) !");
    }

    try {
        $settingModel = new InvoiceSetting();

        $setting = $settingModel->getSetting();

        $page->assign('profileId', $profileId);
        $page->assign('setting', $setting);
        $page->display('invoice.history.create.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
