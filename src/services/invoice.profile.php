<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

SmsApiAdmin::filterAccess();
$logger = Logger::getLogger("service");
$page = SmsApiAdmin::getTemplate();

try {

    $archived        = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
    $model           = new InvoiceProfile();

    $includeArchived = strtolower($archived) === 'archived';
    $profiles        = $model->all($includeArchived);
    $rowspan         = [];

    foreach ($profiles as $profile) {
        if (empty($rowspan[$profile->customerId])) {
            $rowspan[$profile->customerId] = 1;
            $profile->print = true;
        } else {
            $rowspan[$profile->customerId]++;
        }
    }

    $page->assign('rowspan', $rowspan);
    $page->assign('archived', $archived);
    $page->assign('profiles', $profiles);
    $page->display('invoice.profile.tpl');

} catch (Exception $e) {
    $logger->error("$e");
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
