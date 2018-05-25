<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

$service = new AppJsonService();
$logger = Logger::getLogger("service");
$settingModel = new InvoiceSetting();
$invoiceModel = new InvoiceHistory();

try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();
    $errorFields = [];

    $invoiceId = filter_input(INPUT_POST, 'invoiceId', FILTER_VALIDATE_INT);

    if (empty($invoiceId)) {
        SmsApiAdmin::returnError("Invalid Invoice ID ($invoiceId) !");
    }

    if (!$invoice = $invoiceModel->find($invoiceId)) {
        SmsApiAdmin::returnError("Invoice Not Found !");
    }

    $invoice->createInvoiceFile();
    $invoice->update(['status' => InvoiceHistory::INVOICE_LOCK]);

    $service->attach('invoice', $invoice);
    $service->summarise('Invoice successfully locked');
    $service->setStatus(true);
    $service->deliver();
} catch (Exception $e) {
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
