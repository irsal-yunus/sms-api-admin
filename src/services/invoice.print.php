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
    $invoiceId = filter_input(INPUT_GET, 'invoiceId', FILTER_VALIDATE_INT);

    if (empty($invoiceId)) {
        SmsApiAdmin::returnError("Invalid Invoice ID ($invoiceId) !");
    }

    $historyModel = new InvoiceHistory();
    $profileModel = new InvoiceProfile();
    $settingModel = new InvoiceSetting();

    $history = $historyModel->withProduct($invoiceId);

    if (empty($history)) {
        SmsApiAdmin::returnError("Invoice not found !");
    }

    $history = $history[0];

    if (!$history->isLock()) {
        $history->createInvoiceFile();
    }

    if (!empty($_GET['download'])) {
        return $history->downloadFile();
    } else {
        return $history->previewFile();
    }
} catch (Exception $e) {
    $logger->error($e->getMessage());
    SmsApiAdmin::returnError($e->getMessage());
}
