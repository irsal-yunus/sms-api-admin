<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();

    try {
        $client = new ApiBusinessClient();
        $bank = new InvoiceBank();

        $clients = $client->dontHaveInvoiceProfile();
        $banks = $bank->all();

        $page->assign('banks', array_column($banks, 'bankName', 'bankId'));
        $page->assign('clients', $clients);
        $page->display('invoice.profile.create.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
