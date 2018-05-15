<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();

try {
    $errorFields = [];
    $definitions = [
        "bankName" => FILTER_SANITIZE_STRING,
        "accountName" => FILTER_SANITIZE_STRING,
        "accountNumber" => FILTER_SANITIZE_STRING,
        "address" => FILTER_SANITIZE_STRING,
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

    $model = new InvoiceBank();

    if (empty($newData['bankName'])) {
        $errorFields['bankName'] = 'Bank Name should not be empty!';
    }

    if (empty($newData['accountName'])) {
        $errorFields['accountName'] = 'Account Name should not be empty!';
    }

    if (empty($newData['accountNumber'])) {
        $errorFields['accountNumber'] = 'Account Number should not be empty!';
    } else if ($model->isAccountNumberDuplicate($newData['accountNumber'])) {
        $errorFields['accountNumber'] = 'Account Number already exists';
    }

    if ($errorFields) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    } else {
        $bankId = $model->insert($newData);
        $service->setStatus(true);
        $service->attach('bankId', $bankId);
        $service->summarise('Bank Account successfully added');
        $service->deliver();
    }
} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
