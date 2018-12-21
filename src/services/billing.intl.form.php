<?php

require_once '../../vendor/autoload.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiCountry.php';

use Firstwap\SmsApiAdmin\lib\model\InternationalPrice;

SmsApiAdmin::filterAccess();

$logger    = \Logger::getRootLogger();
$page      = \SmsApiAdmin::getTemplate();
$intlPrice = new InternationalPrice();

try {
    $priceId     = filter_input(INPUT_POST, 'price_id', FILTER_VALIDATE_INT);

    if ($priceId)
    {
        if ($price = $intlPrice->find($priceId))
        {
            $page->assign('price', $price);
        }
    }

    $countries = ApiCountry::getInternationalPriceCountry(InternationalPrice::DEFAULT_PRICE_COUNTRY_CODE, $priceId);

    $page->assign('countries', $countries);
    $page->display('billing.intl.form.tpl');
}
catch (Exception $e)
{
    $logger->error($e);
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    \SmsApiAdmin::returnError($e->getMessage());
}
