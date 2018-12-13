<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

SmsApiAdmin::filterAccess();

$logger     = Logger::getRootLogger();
$service    = new AppJsonService();
$model      = new InvoiceProfile();

try {
    $errorFields = [];
    $definitions = [
        "clientId" => FILTER_SANITIZE_NUMBER_INT,
        "bankId" => FILTER_SANITIZE_NUMBER_INT,
        "autoGenerate" => FILTER_SANITIZE_NUMBER_INT,
        "approvedName" => FILTER_SANITIZE_STRING,
        "approvedPosition" => FILTER_SANITIZE_STRING,
        "profileName" => FILTER_SANITIZE_STRING,
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

    if (empty($newData['profileName'])) {
        $errorFields['profileName'] = 'Profile Name should not be empty!';
    }
    elseif ($model->isProfileNameDuplicate($newData['profileName'])) {
        $errorFields['profileName'] = 'Profile Name already exists!';
    }

    if (empty($newData['clientId'])) {
        $errorFields['clientId'] = 'Client Name should not be empty!';
    }

    if (empty($newData['bankId'])) {
        $errorFields['bankId'] = 'Payment Detail should not be empty!';
    }

    if ($errorFields) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    } else {
        $profileId = $model->insert($newData);
        $service->setStatus(true);
        $service->attach('profileId', $profileId);
        $service->summarise('Invoice Profile successfully added');
        $service->deliver();
    }
} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
