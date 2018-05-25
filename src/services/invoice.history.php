<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();
    $profileId = filter_input(INPUT_POST, 'profileId', FILTER_VALIDATE_INT);

    if (empty($profileId)) {
        SmsApiAdmin::returnError("Invalid Invoice Profile ID ($profileId) !");
    }

    try {
        $profileModel = new InvoiceProfile();
        $settingModel = new InvoiceSetting();
        $historyModel = new InvoiceHistory();

        $profile = $profileModel->find($profileId);

        if (empty($profile)) {
            SmsApiAdmin::returnError("Invoice Profile not found !");
        }

        $apiUsers = $profile->loadApiUsers();

        if (is_array($apiUsers)) {
            $apiUsers = implode(', ', array_column($apiUsers, 'userName'));
        }

        $invoices = $historyModel->whereProfile($profileId);
        $setting = $settingModel->getSetting();

        $page->assign('apiUsers', $apiUsers);
        $page->assign('profile', $profile);
        $page->assign('invoices', $invoices);
        $page->assign('setting', $setting);
        $page->display('invoice.history.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
