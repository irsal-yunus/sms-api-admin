<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;

try {
    SmsApiAdmin::filterAccess();

    $logger = Logger::getLogger("service");
    $page = SmsApiAdmin::getTemplate();
    $invoiceType = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

    $historyModel = new InvoiceHistory();

    $pending = $historyModel->pendingCount();
    $invoices = $historyModel->whereStatus($invoiceType);

    $page->assign('type', $invoiceType);
    $page->assign('pending', $pending);
    $page->assign('invoices', $invoices);
    $page->display('invoice.table.tpl');
} catch (Exception $e) {
    $logger->error($e->getMessage());
    SmsApiAdmin::returnError($e->getMessage());
}
