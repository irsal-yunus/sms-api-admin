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

    if (empty($updateData['profileId'])) {
        SmsApiAdmin::returnError("Invalid Profile ID ($profileId) !");
    }

    if (empty($updateData['startDate'])) {
        $errorFields['startDate'] = 'Invoice Date should not be empty!';
    } else if (strtotime($updateData['startDate']) === false) {
        $errorFields['startDate'] = 'Invalid Invoice Date Format!';
    }
    // else if (
    //     $invoiceModel->isInvoiceAlreadyExists(
    //         $updateData['startDate'],
    //         $updateData['profileId'],
    //         $updateData['invoiceId']
    //     )
    // ) {
    //     $date = date('1 - t F Y', strtotime("{$updateData['startDate']} -1 month"));
    //     $errorFields['startDate'] = "Invoice for billing " . $date . " already exists !";
    // }

    if (empty($updateData['paymentPeriod'])) {
        $errorFields['paymentPeriod'] = 'Term of Payment should not be empty!';
    }

    if (empty($updateData['invoiceNumber'])) {
        $errorFields['invoiceNumber'] = 'Invoice Number should not be empty!';
    } else if ($invoiceModel->isInvoiceNumberDuplicate($updateData['invoiceNumber'], $updateData['invoiceId'])) {
        $errorFields['invoiceNumber'] = 'Invoice Number already exists!';
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

    try {
        $invoiceModel->updateHistory($updateData['invoiceId'], $updateData);
        $setting = $settingModel->getSetting();
        $settingModel->refreshInvoiceNumber();

        $service->attach('invoiceId', $updateData['invoiceId']);
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
