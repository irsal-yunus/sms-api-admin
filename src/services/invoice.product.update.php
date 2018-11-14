<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;

SmsApiAdmin::filterAccess();
$logger = Logger::getRootLogger();
$service = new AppJsonService();

function convertCurrencyString($string = '')
{
    return floatval(str_replace(',', '', $string));
}

try {
    $errorFields = [];
    $updateData = filter_input_array(INPUT_POST, [
        "productName" => FILTER_SANITIZE_STRING,
        "reportName"  => FILTER_SANITIZE_STRING,
        "ownerType"   => FILTER_SANITIZE_STRING,
        "ownerId"     => FILTER_SANITIZE_NUMBER_INT,
        "period"      => FILTER_SANITIZE_STRING,
        "isPeriod"    => FILTER_SANITIZE_NUMBER_INT,
        "productId"   => FILTER_SANITIZE_NUMBER_INT,
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

    foreach ($updateData as $key => $value) {
        if ($value === null) {
            unset($updateData[$key]);
        }
    }

    if (!$updateData) {
        $service->setStatus(false);
        $service->summarise('No data to add');
        $service->deliver();
    }

    if ($updateData['isPeriod'] === 0) {
        $date       = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $timestamp  = strtotime($date);

        if ($timestamp === false) {
            $errorFields['date']  = 'Date input is wrong format';
        } else {
            $updateData['period'] = date('Y-m-d', $timestamp);
        }
    }

    if (empty($updateData['productId'])) {
        $errorFields['productId'] = 'Product ID should not be empty!';
    }

    if (empty($updateData['productName'])) {
        $errorFields['productName'] = 'Product Name should not be empty!';
    }

    if (empty($updateData['ownerId'])) {
        $errorFields['ownerId'] = 'Owner ID should not be empty!';
    }

    if (empty($updateData['ownerType'])) {
        $errorFields['ownerType'] = 'Owner Type should not be empty!';
    } else if (!in_array($updateData['ownerType'], [InvoiceProduct::PROFILE_PRODUCT, InvoiceProduct::HISTORY_PRODUCT])) {
        $errorFields['ownerType'] = 'Invalid Owner Type value!';
    }

    if (isset($updateData['useReport'])) {
        if ($updateData['useReport'] == 1) {
            if (empty($updateData["reportName"])) {
                $errorFields['reportName'] = 'Report Name should not be empty when using report !';
            } else {
                if (empty($updateData['manualInput'])) {
                    $updateData['unitPrice'] = 0;
                    $updateData['qty'] = 0;
                } else {
                    $updateData['useReport'] = 2;
                    unset($updateData['manualInput']);
                }
            }

            if ($updateData['ownerType'] === InvoiceProduct::HISTORY_PRODUCT
                && strtotime($updateData['period']) === false) {
                $errorFields['period'] = 'Invalid period Type value!';
            }

        } else {
            $updateData["reportName"] = null;
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
        $model->updateProduct($updateData['productId'], $updateData);

        if ($model->isHistory()) {
            $invoiceModel = new InvoiceHistory;

            if ($invoice = $invoiceModel->find($model->ownerId)) {
                $invoice->createInvoiceFile();
            }
        }

        $service->setStatus(true);
        $service->attachRaw($updateData);
        $service->summarise('Product successfully updated');
        $service->deliver();
    }
} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
