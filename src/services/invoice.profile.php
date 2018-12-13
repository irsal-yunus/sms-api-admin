<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

SmsApiAdmin::filterAccess();
$logger = Logger::getLogger("service");
$page   = SmsApiAdmin::getTemplate();

try {
    $model           = new InvoiceProfile();
    $archived        = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
    $invoicePage     = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) ?? 1;
    $includeArchived = strtolower($archived) === 'archived';
    $profiles        = $model->all($includeArchived);
    $rowspan         = [];
    $paginate        = $model->getProfilebyPage($archived, $invoicePage);
    $chunk           = LIMIT_PER_PAGE;
    $pageCount       = ceil($paginate['total'] / $chunk);

    $numberFiles = [
        'firstNumber' => 0,
        'endNumber'   => 0,
    ];

    if ($paginate['total'] !== '0')
    {
        if ($invoicePage == 1)
        {
            $numberFiles['firstNumber'] = 1;
            $numberFiles['endNumber']   = count($paginate['data']);
        }
        elseif ($invoicePage == $pageCount)
        {
            $numberFiles['firstNumber'] = ($invoicePage * $chunk) - ($chunk - 1);
            $numberFiles['endNumber']   = $paginate['total'];
        }
        else
        {
            $numberFiles['firstNumber'] = ($invoicePage * $chunk) - ($chunk - 1);
            $numberFiles['endNumber']   = ($invoicePage * $chunk);
        }
    }

    foreach ($paginate['data'] as &$profile)
    {
        if (empty($rowspan[$profile->clientId]))
        {
            $rowspan[$profile->clientId] = 1;
            $profile->print              = true;
        }
        else
        {
            $rowspan[$profile->clientId]++;
        }
    }

    $page->assign('numberFiles', $numberFiles);
    $page->assign('archived', $archived);
    $page->assign('profiles', $paginate['data']);
    $page->assign('totalData', $paginate['total']);
    $page->assign('pending', $pending);
    $page->assign('pageCount', $pageCount);
    $page->assign('page', $invoicePage);
    $page->assign('rowspan', $rowspan);
    $page->display('invoice.profile.tpl');

}
catch (Exception $e)
{
    $logger->error("$e");
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
