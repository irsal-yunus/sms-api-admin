<?php

require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InternationalPrice;

SmsApiAdmin::filterAccess();

function convertCurrencyString($string)
{
    return floatval(str_replace(',', '', $string));
}

$logger    = Logger::getRootLogger();
$page      = SmsApiAdmin::getTemplate();
$service   = new AppJsonService();
$intlPrice = new InternationalPrice();

try {
    $inputs = filter_input_array(INPUT_POST, [
        "billingInternationalPriceId" => FILTER_SANITIZE_NUMBER_INT,
        "unitPrice"              => ['filter' => FILTER_CALLBACK, 'options' => 'convertCurrencyString'],
        "countryCodeRef"         => FILTER_SANITIZE_STRING,
    ]);

    if (empty($inputs['unitPrice']))
    {
        $errorFields['unitPrice'] = 'Unit Price should not be empty!';
    }

    if (empty($inputs['countryCodeRef']))
    {
        $errorFields['countryCodeRef'] = 'Country Name should not be empty!';
    }

    if ($errorFields)
    {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    }

    if (!empty($inputs['billingInternationalPriceId']))
    {
        if (!$price = $intlPrice->find($inputs['billingInternationalPriceId']))
        {
            SmsApiAdmin::returnError("International Price data is not found");
        }

        $price->update($inputs);
        $service->summarise('International Price successfully Updated');
    }
    else
    {
        $intlPrice->save($inputs);
        $service->summarise('International Price successfully added');
    }

    $service->setStatus(true);
    $service->deliver();
}
catch (Exception $e)
{
    $logger->error($e);
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    SmsApiAdmin::returnError($e->getMessage());
}
