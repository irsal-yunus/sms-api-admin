<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiBusinessClient.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();

    $profileId = filter_input(INPUT_POST, 'profileId', FILTER_VALIDATE_INT);

    if (empty($profileId)) {
        SmsApiAdmin::returnError("Invalid Invoice Profile ID ($profileId) !");
    }

    $profileModel = new InvoiceProfile();

    if (!$profile = $profileModel->find($profileId)) {
        SmsApiAdmin::returnError("Invoice Profile not found !");
    }

    $client = new ApiBusinessClient();
    $bank = new InvoiceBank();

    $clients = $client->getSelectClient($profile->clientId);
    $banks = $bank->all();

    $page->assign('profile', $profile);
    $page->assign('banks', array_column($banks, 'bankName', 'bankId'));
    $page->assign('clients', $clients);
    $page->display('invoice.profile.edit.tpl');

} catch (Exception $e) {
    $logger->error("$e");
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
