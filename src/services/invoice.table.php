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
    $invoicePage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;

    if (strval($invoicePage) !== strval(intval($invoicePage))) {
        $invoicePage=1;
    }


    $historyModel = new InvoiceHistory();

    $pending            =   $historyModel->pendingCount();
    $paginate           =   $historyModel->getHistorybyPage($invoiceType, $invoicePage);
    $chunk              =   25;
    $pageCount          =   ceil($paginate['total'] / $chunk);

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
    $page->assign('type',       $invoiceType);
    $page->assign('page',       $invoicePage);
    $page->assign('pending',    $pending);
    $page->assign('totalData',  $paginate['total']);
    $page->assign('invoices',   $paginate['data']);
    $page->assign('pageCount',  $pageCount);
    $page->display('invoice.table.tpl');
} catch (Exception $e) {
    $logger->error($e->getMessage());
    SmsApiAdmin::returnError($e->getMessage());
}
