<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;
use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();
    try {
        $settingModel = new InvoiceSetting();
        $bankModel = new InvoiceBank();

        $setting = $settingModel->getSetting();
        $banks = $bankModel->all();

        $page->assign('setting', $setting);
        $page->assign('banks', $banks);
        $page->display('invoice.setting.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
