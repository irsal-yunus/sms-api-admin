<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;

SmsApiAdmin::filterAccess();
$logger = Logger::getLogger("service");

try {

    $client  = new ApiBusinessClient();
    $bank    = new InvoiceBank();

    $clients = $client->getSelectClient();
    $banks   = $bank->all();

    $page    = SmsApiAdmin::getTemplate();

    $page->assign('banks', array_column($banks, 'bankName', 'bankId'));
    $page->assign('clients', $clients);
    $page->display('invoice.profile.create.tpl');

} catch (Exception $e) {
    $logger->error("$e");
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
