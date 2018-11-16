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

    $archived       = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
    $invoicePage    = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;

    if (strval($invoicePage) !== strval(intval($invoicePage))) {
        $invoicePage=1;
    }

    try {
        $model      = new InvoiceProfile();
        $invoice    = new InvoiceHistory();

        $pending    =   $invoice->pendingCount();
        $paginate   =   $model->getProfilebyPage($archived, $invoicePage);
        $chunk      =   25;
        $pageCount  =   ceil($paginate['total']/$chunk);

        $numberFiles = [
            'firstNumber'  => 0,
            'endNumber'    => 0
        ];

        if ($paginate['total']!=='0') {
            if ($invoicePage==1)
            {
                $numberFiles['firstNumber'] = 1;
                $numberFiles['endNumber']= count($paginate['data']);
            }
            else if ($invoicePage == $pageCount)
            {
                $numberFiles['firstNumber'] = ($invoicePage*$chunk)-($chunk-1);
                $numberFiles['endNumber']   = $paginate['total'];
            }
            else
            {
                $numberFiles['firstNumber'] = ($invoicePage*$chunk)-($chunk-1);
                $numberFiles['endNumber']   = ($invoicePage*$chunk);
            }
        }

        $page->assign('numberFiles',$numberFiles);
        $page->assign('archived',   $archived);
        $page->assign('profiles',   $paginate['data']);
        $page->assign('totalData',  $paginate['total']);
        $page->assign('pending',    $pending);
        $page->assign('pageCount',  $pageCount);
        $page->assign('page',       $invoicePage);
        $page->display('invoice.profile.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
