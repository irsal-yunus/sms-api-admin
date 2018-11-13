<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();

    $profileId = filter_input(INPUT_POST, 'profileId', FILTER_VALIDATE_INT);

    if (empty($profileId)) {
        $profilesModel = new InvoiceProfile();

        $profiles = $profilesModel->all(1);
        $page->assign('profiles', array_column($profiles, 'companyName', 'profileId'));
    } else {
        $page->assign('profileId', $profileId);
    }

    $settingModel = new InvoiceSetting();
    $setting = $settingModel->getSetting();

    $page->assign('setting', $setting);
    $page->display('invoice.history.create.tpl');
} catch (Exception $e) {
    $logger->error("$e");
    SmsApiAdmin::returnError($e->getMessage());
}
