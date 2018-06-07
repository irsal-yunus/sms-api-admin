<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceBank;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $bankId = filter_input(INPUT_POST, 'bankId', FILTER_VALIDATE_INT);
    if (empty($bankId)) {
        SmsApiAdmin::returnError("Invalid Bank ID ($bankId) !");
    }

    $page = SmsApiAdmin::getTemplate();

    try {
        $bankModel = new InvoiceBank();
        $bank = $bankModel->find($bankId);

        if (empty($bank)) {
            SmsApiAdmin::returnError("Bank account not found !");
        }

        $page->assign('bank', $bank);
        $page->display('invoice.bank.edit.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
