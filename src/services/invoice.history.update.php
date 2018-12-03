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
        "invoiceId" => FILTER_VALIDATE_INT,
        "profileId" => FILTER_VALIDATE_INT,
        "invoiceNumber" => FILTER_VALIDATE_INT,
        "startDate" => FILTER_SANITIZE_STRING,
        "paymentPeriod" => FILTER_VALIDATE_INT,
        "refNumber" => FILTER_SANITIZE_STRING,
    ];
    $updateData = filter_input_array(INPUT_POST, $definitions);

    foreach ($updateData as $key => $value) {
        if ($value === null) {
            unset($updateData[$key]);
        }
    }

    if (!$updateData) {
        $service->setStatus(false);
        $service->summarise('No data to add');
        $service->deliver();
    }

    if (empty($updateData['invoiceId'])) {
        SmsApiAdmin::returnError("Invalid ID ($invoiceId) !");
    }

    if (empty($updateData['startDate'])) {
        $errorFields['startDate'] = 'Invoice Date should not be empty!';
    } else if (strtotime($updateData['startDate']) === false) {
        $errorFields['startDate'] = 'Invalid Invoice Date Format!';
    }

    if (empty($updateData['paymentPeriod'])) {
        $errorFields['paymentPeriod'] = 'Term of Payment should not be empty!';
    }

    if (!$invoice = $invoiceModel->find($updateData['invoiceId'])) {
        SmsApiAdmin::returnError("Invalid Not Found !");
    }

    if ($invoice->isLock()) {
        SmsApiAdmin::returnError("Invoice is already locked. You can't update it !");
    }

    if ($invoice->invoiceType === InvoiceHistory::ORIGINAL) {
        if (empty($updateData['invoiceNumber'])) {
            $errorFields['invoiceNumber'] = 'Invoice Number should not be empty!';
        } else if ($invoiceModel->isInvoiceNumberDuplicate($updateData['invoiceNumber'], $updateData['invoiceId'])) {
            $errorFields['invoiceNumber'] = 'Invoice Number already exists!';
        }
    } else {
        // prevent update invoice number if invoice type is not original
        unset($updateData['invoiceNumber']);
    }

    if (!empty($errorFields)) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    }

    $dueDate = strtotime("{$updateData['startDate']} {$updateData['paymentPeriod']}days");
    $updateData['dueDate'] = date('Y-m-d', $dueDate);
    unset($updateData['paymentPeriod']);

    $updateData['updatedAt'] = date('Y-m-d H:i:s');

    if ($invoice->update($updateData)) {
        $invoice->createInvoiceFile();
    }


    $setting = $settingModel->getSetting();
    $settingModel->refreshInvoiceNumber();

    $service->attach('invoiceId', $updateData['invoiceId']);
    $service->summarise('Invoice successfully added');
    $service->setStatus(true);
    $service->deliver();


} catch (Exception $e) {
    $logger->error("$e");
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
