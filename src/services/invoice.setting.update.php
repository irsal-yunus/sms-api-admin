<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();

try {
    $errorFields = [];
    $definitions = [
        "settingId" => FILTER_SANITIZE_NUMBER_INT,
        "paymentPeriod" => FILTER_SANITIZE_NUMBER_INT,
        "lastInvoiceNumber" => FILTER_SANITIZE_NUMBER_INT,
        "invoiceNumberPrefix" => FILTER_SANITIZE_STRING,
        "authorizedName" => FILTER_SANITIZE_STRING,
        "authorizedPosition" => FILTER_SANITIZE_STRING,
        "approvedName" => FILTER_SANITIZE_STRING,
        "approvedPosition" => FILTER_SANITIZE_STRING,
        "noteMessage" => FILTER_SANITIZE_STRING,
    ];
    $updates = filter_input_array(INPUT_POST, $definitions);
    foreach ($updates as $key => $value) {
        if ($value === null) {
            unset($updates[$key]);
        }
    }

    if (!$updates) {
        $service->setStatus(false);
        $service->summarise('No update fields');
        $service->deliver();
    }

    if (empty($updates['paymentPeriod']) || $updates['paymentPeriod'] < 1) {
        $errorFields['paymentPeriod'] = 'Term of payment should not be empty!';
    }

    if ($errorFields) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    } else {
        $model = new InvoiceSetting();
        $model->updateSetting($updates);
        $service->setStatus(true);
        $service->summarise('Invoice Setting successfully updated');
        $service->deliver();
    }
} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
