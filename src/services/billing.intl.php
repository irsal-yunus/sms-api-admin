<?php

require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InternationalPrice;

SmsApiAdmin::filterAccess();

$logger = Logger::getRootLogger();
$page   = SmsApiAdmin::getTemplate();
$model  = new InternationalPrice();

try {
    $prices       = $model->all();
    $defaultPrice = array_shift($prices);

    $page->assign('defaultPrice', $defaultPrice);
    $page->assign('prices', $prices);
    $page->display('billing.intl.tpl');
}
catch (Exception $e)
{
    $logger->error($e);
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
