<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;
use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();

    $invoiceId = filter_input(INPUT_POST, 'invoiceId', FILTER_VALIDATE_INT);

    if (empty($invoiceId)) {
        SmsApiAdmin::returnError("Invalid ID ($invoiceId) !");
    }

    try {
        $settingModel = new InvoiceSetting();
        $invoiceModel = new InvoiceHistory();

        if (!$invoice = $invoiceModel->find($invoiceId)) {
            SmsApiAdmin::returnError("Invoice not found !");
        }

        $setting = $settingModel->getSetting();

        $page->assign('invoice', $invoice);
        $page->assign('setting', $setting);
        $page->display('invoice.history.edit.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
