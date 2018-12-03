<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;

SmsApiAdmin::filterAccess();

$logger     = Logger::getRootLogger();
$service    = new AppJsonService();

function convertCurrencyString($string)
{
    return floatval(str_replace(',', '', $string));
}

try {
    $errorFields = [];
    $newData = filter_input_array(INPUT_POST, [
        "productName" => FILTER_SANITIZE_STRING,
        "reportName"  => FILTER_SANITIZE_STRING,
        "ownerType"   => FILTER_SANITIZE_STRING,
        "ownerId"     => FILTER_SANITIZE_NUMBER_INT,
        "period"      => FILTER_SANITIZE_STRING,
        "isPeriod"    => FILTER_SANITIZE_NUMBER_INT,
        "qty" => [
            'filter'  => FILTER_CALLBACK,
            'options' => 'convertCurrencyString',
        ],
        "unitPrice"   => [
            'filter'  => FILTER_CALLBACK,
            'options' => 'convertCurrencyString',
        ],
        "useReport"   => FILTER_SANITIZE_NUMBER_INT,
        "manualInput" => FILTER_SANITIZE_NUMBER_INT,
    ]);

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

    if ($newData['isPeriod'] === 0) {
        $date       = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $timestamp  = strtotime($date);

        if ($timestamp === false) {
            $errorFields['date'] = 'Date input is wrong format';
        } else {
            $newData['period'] = date('Y-m-d', $timestamp);
        }
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
                if (empty($newData['manualInput'])) {
                    $newData['unitPrice'] = 0;
                    $newData['qty'] = 0;
                } else {
                    $newData['useReport'] = 2;
                    unset($newData['manualInput']);
                }
            }
            if ($newData['ownerType'] === InvoiceProduct::HISTORY_PRODUCT
                && strtotime($newData['period']) === false) {
                $errorFields['period'] = 'Invalid period Type value!';
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
        $model->insertProduct($newData);

        if ($model->isHistory()) {
            $invoiceModel = new InvoiceHistory;

            if ($invoice = $invoiceModel->find($model->ownerId)) {
                $invoice->createInvoiceFile();
            }

            $logger->info($invoice);
        }

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
