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

    $bankId = filter_input(INPUT_POST, 'bankId', FILTER_VALIDATE_INT);

    if (empty($bankId)) {
        SmsApiAdmin::returnError("Invalid Bank ID ($bankId) !");
    }

    $errorFields = [];
    $definitions = [
        "bankName" => FILTER_SANITIZE_STRING,
        "accountName" => FILTER_SANITIZE_STRING,
        "accountNumber" => FILTER_SANITIZE_STRING,
        "address" => FILTER_SANITIZE_STRING,
    ];
    $updateData = filter_input_array(INPUT_POST, $definitions);
    foreach ($updateData as $key => $value) {
        if ($value === null) {
            unset($updateData[$key]);
        }
    }

    $model = new InvoiceBank();

    if (!$updateData) {
        $service->setStatus(false);
        $service->summarise('No data to update');
        $service->deliver();
    }

    if (empty($updateData['bankName'])) {
        $errorFields['bankName'] = 'Bank Name should not be empty!';
    }

    if (empty($updateData['accountName'])) {
        $errorFields['accountName'] = 'Account Name should not be empty!';
    }

    if (empty($updateData['accountNumber'])) {
        $errorFields['accountNumber'] = 'Account Number should not be empty!';
    } else if ($model->isAccountNumberDuplicate($updateData['accountNumber'], $bankId)) {
        $errorFields['accountNumber'] = 'Account Number already exists';
    }

    if ($errorFields) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    } else {
        $model->updateBank($bankId, $updateData);
        $service->setStatus(true);
        $service->summarise('Bank Account successfully updated');
        $service->deliver();
    }
} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
