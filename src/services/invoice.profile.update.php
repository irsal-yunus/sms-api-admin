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
    $profileId = filter_input(INPUT_POST, 'profileId', FILTER_VALIDATE_INT);
    $bankId = filter_input(INPUT_POST, 'bankId', FILTER_VALIDATE_INT);

    if (empty($profileId)) {
        SmsApiAdmin::returnError("Invalid Invoice Profile ID ($profileId) !");
    }

    if (empty($bankId)) {
        $errorFields['bankId'] = 'Payment Detail should not be empty!';
    }

    if ($errorFields) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    } else {
        $model = new InvoiceProfile();
        $model->updateProfile($profileId, ['bankId' => $bankId]);
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
