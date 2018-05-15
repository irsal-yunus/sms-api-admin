<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();

function convertCurrencyString($string)
{
    return floatval(str_replace(',', '', $string));
}

try {
    $errorFields = [];

    $definitions = [
        "productName" => FILTER_SANITIZE_STRING,
        "reportName" => FILTER_SANITIZE_STRING,
        "ownerType" => FILTER_SANITIZE_STRING,
        "ownerId" => FILTER_SANITIZE_NUMBER_INT,
        "qty" => [
            'filter' => FILTER_CALLBACK,
            'options' => 'convertCurrencyString',
        ],
        "unitPrice" => [
            'filter' => FILTER_CALLBACK,
            'options' => 'convertCurrencyString',
        ],
        "useReport" => FILTER_SANITIZE_NUMBER_INT,
    ];

    $newData = filter_input_array(INPUT_POST, $definitions);

    foreach ($newData as $key => $value) {
        if ($value === null) {
            unset($newData[$key]);
        }
    }

    if (!$newData) {
        $service->setStatus(false);
        $service->summarise('No data to add');
        $service->deliver();
    }

    if (empty($newData['productName'])) {
        $errorFields['productName'] = 'Product Name should not be empty!';
    }

    if (empty($newData['ownerId'])) {
        $errorFields['ownerId'] = 'Owner ID should not be empty!';
    }

    if (empty($newData['ownerType'])) {
        $errorFields['ownerType'] = 'Owner Type should not be empty!';
    } else if (!in_array($newData['ownerType'], [InvoiceProduct::PROFILE_PRODUCT, InvoiceProduct::HISTORY_PRODUCT])) {
        $errorFields['ownerType'] = 'Invalid Owner Type value!';
    }

    if (isset($newData['useReport'])) {
        if ($newData['useReport'] == 1) {
            if (empty($newData["reportName"])) {
                $errorFields['reportName'] = 'Report Name should not be empty when using report !';
            } else {
                $newData['unitPrice'] = 0;
                $newData['qty'] = 0;
            }
        }
    } else {
        $errorFields['useReport'] = 'Use Report should not be empty!';
    }

    if ($errorFields) {
        $service->setStatus(false);
        $service->summarise('Input fields error');
        $service->attachRaw($errorFields);
        $service->deliver();
    } else {
        $model = new InvoiceProduct();
        $model->insert($newData);
        $service->setStatus(true);
        $service->summarise('Product successfully added');
        $service->deliver();
    }
} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
