<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();

try {

    $errorFields = [];
    $definitions = [
        "profileId" => FILTER_SANITIZE_NUMBER_INT,
        "bankId" => FILTER_SANITIZE_NUMBER_INT,
        "approvedName" => FILTER_SANITIZE_STRING,
        "approvedPosition" => FILTER_SANITIZE_STRING,
    ];

    $updates = filter_input_array(INPUT_POST, $definitions);

    if (empty($updates['profileId'])) {
        SmsApiAdmin::returnError("Invalid Invoice Profile ID ({$updates['profileId']}) !");
    }

    if (empty($updates['bankId'])) {
        $errorFields['bankId'] = 'Payment Detail should not be empty!';
    }

    if ($errorFields) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    } else {
        $model = new InvoiceProfile();
        $model->updateProfile($updates['profileId'], $updates);
        $service->setStatus(true);
        $service->summarise('Invoice Profile successfully updated');
        $service->deliver();
    }
} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
