<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;

/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();
    try {
        $historyModel = new InvoiceHistory();
        $pending = $historyModel->pendingCount();

        $page->assign('pending', $pending);
        $page->display('invoice.view.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
