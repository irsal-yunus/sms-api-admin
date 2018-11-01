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

    $archived = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
    try {
        $model = new InvoiceProfile();
        $invoice = new InvoiceHistory();

        $pending = $invoice->pendingCount();
        $profiles=($archived==='archived') ? $model->all() :  $profiles = $model->all(1);
        $page->assign('archived',$archived);
        $page->assign('profiles', $profiles);
        $page->assign('pending', $pending);
        $page->display('invoice.profile.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
