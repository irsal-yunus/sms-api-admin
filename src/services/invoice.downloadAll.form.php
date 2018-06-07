<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();

    try {
        $dates = [];
        $last = strtotime('first day of this month');
        $start = strtotime('2018-01-01');

        while ($start <= $last) {
            $dates[date('Y',$last)][$last] = date('F', $last);
            $last = strtotime('-1 month', $last);
        }

        $page->assign('dates', $dates);
        $page->assign('current', current(array_keys($dates)));
        $page->display('invoice.downloadAll.form.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
