<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
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
        $model = new InvoiceProfile();
        $invoice = new InvoiceHistory();

        $pending = $invoice->pendingCount();
        $profiles = $model->all();

        $page->assign('profiles', $profiles);
        $page->assign('pending', $pending);
        $page->display('invoice.profile.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
