<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;

$logger = Logger::getLogger("service");
$service = new AppJsonService();

try {
    SmsApiAdmin::filterAccess();

    $invoiceId = filter_input(INPUT_POST, 'invoiceId', FILTER_VALIDATE_INT);

    if (empty($invoiceId)) {
        SmsApiAdmin::returnError("Invalid Invoice ID ($invoiceId) !");
    }

    $page = SmsApiAdmin::getTemplate();

    try {
        $invoiceModel = new InvoiceHistory();
        $invoice = $invoiceModel->find($invoiceId);

        if (empty($invoice)) {
            SmsApiAdmin::returnError("Invoice not found !");
        }
        $invoice->deleteWithProduct();
        $service->setStatus(true);
        $service->summarise('Invoice successfully deleted');
        $service->deliver();
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
    SmsApiAdmin::returnError('Interval Server Error');
}
