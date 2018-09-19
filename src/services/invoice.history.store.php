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
    $definitions = [
        "profileId" => FILTER_VALIDATE_INT,
        "invoiceNumber" => FILTER_VALIDATE_INT,
        "startDate" => FILTER_SANITIZE_STRING,
        "paymentPeriod" => FILTER_VALIDATE_INT,
        "refNumber" => FILTER_SANITIZE_STRING,
    ];
    $newData = filter_input_array(INPUT_POST, $definitions);

    foreach ($newData as $key => $value) {
        if ($value === null) {
            unset($newData[$key]);
        }
    }

    if (!$newData) {
        $service->setStatus(false);
        $service->summarise('No data to add');
        $service->deliver();
    }

    if (empty($newData['profileId'])) {
        SmsApiAdmin::returnError("Invalid Profile ID ($profileId) !");
    }

    if (empty($newData['startDate'])) {
        $errorFields['startDate'] = 'Invoice Date should not be empty!';
    } else if (strtotime($newData['startDate']) === false) {
        $errorFields['startDate'] = 'Invalid Invoice Date Format!';
    }

    if (empty($newData['paymentPeriod'])) {
        $errorFields['paymentPeriod'] = 'Term of Payment should not be empty!';
    }

    if (empty($newData['invoiceNumber'])) {
        $errorFields['invoiceNumber'] = 'Invoice Number should not be empty!';
    } else if ($invoiceModel->isInvoiceNumberDuplicate($newData['invoiceNumber'])) {
        $errorFields['invoiceNumber'] = 'Invoice Number already exists!';
    }

    if (!empty($errorFields)) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    }

    $dueDate = strtotime("{$newData['startDate']} {$newData['paymentPeriod']}days");
    $newData['dueDate'] = date('Y-m-d', $dueDate);
    unset($newData['paymentPeriod']);

    try {
        $invoiceId = $invoiceModel->createHistory($newData);
        $settingModel->refreshInvoiceNumber();
        $invoice = $invoiceModel->find($invoiceId);
        $invoice->createInvoiceFile();

        $service->attach('invoiceId', $invoiceId);
        $service->attach('profileId', $newData['profileId']);
        $service->summarise('Invoice successfully added');
        $service->setStatus(true);
        $service->deliver();
    } catch (Exception $e) {
        $logger->error($e->getTraceAsString());
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
