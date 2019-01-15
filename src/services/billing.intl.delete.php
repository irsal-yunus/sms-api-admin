<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InternationalPrice;

SmsApiAdmin::filterAccess();

$logger    = Logger::getLogger("service");
$service   = new AppJsonService();
$intlPrice = new InternationalPrice();

try {
    $priceId = filter_input(INPUT_POST, 'price_id', FILTER_VALIDATE_INT);

    if (empty($priceId))
    {
        SmsApiAdmin::returnError("Invalid International Price ID ($priceId) !");
    }

    if (!$price = $intlPrice->find($priceId))
    {
        SmsApiAdmin::returnError("International Price not found !");
    }

    $price->delete();

    $service->setStatus(true);
    $service->summarise('International Price successfully deleted');
    $service->deliver();
}
catch (Exception $e)
{
    $logger->error($e);
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    \SmsApiAdmin::returnError($e->getMessage());
}
