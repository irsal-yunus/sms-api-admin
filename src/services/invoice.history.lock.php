<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

$service      = new AppJsonService();
$logger       = Logger::getLogger("service");
$settingModel = new InvoiceSetting();
$invoiceModel = new InvoiceHistory();

try {
    SmsApiAdmin::filterAccess();

    $page        = SmsApiAdmin::getTemplate();
    $errorFields = [];

    $invoiceId = filter_input(INPUT_POST, 'invoiceId', FILTER_VALIDATE_INT);
    $inputs = filter_input_array(INPUT_POST, [
        'type'      => FILTER_SANITIZE_STRING, // lock, copy, revise
        'invoiceId' => FILTER_VALIDATE_INT,
    ]);

    if (empty($inputs['invoiceId']))
    {
        SmsApiAdmin::returnError("Invalid Invoice ID ($invoiceId) !");
    }

    if (!$invoice = $invoiceModel->find($inputs['invoiceId']))
    {
        SmsApiAdmin::returnError("Invoice Not Found !");
    }

    $operationType = strtolower($inputs['type']);

    switch ($operationType)
    {
        case 'copy':
        {
            if ($invoice->isExpired())
            {
                SmsApiAdmin::returnError("The invoice already expired on {$invoice->dueDate}");
            }

            $invoice->copyInvoice();
            $service->summarise('Invoice successfully copied');
            break;
        }
        case 'revise':
        {
            if ($invoice->isExpired())
            {
                $paymentPeriod      = $invoice->paymentPeriod();
                $invoice->dueDate   = date('Y-m-d', strtotime("$paymentPeriod days"));
                $invoice->startDate = date('Y-m-d');
            }

            $invoice->reviseInvoice();
            $service->summarise('Add revised invoice successfully');
            break;
        }
        default:
        {
            $invoice->lockInvoice();
            $service->summarise('Invoice successfully locked');
        }
    }

    $service->attach('invoice', $invoice);
    $service->setStatus(true);
    $service->deliver();

} catch (Exception $e) {
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    $logger->error(json_encode($e->getTrace(), JSON_PRETTY_PRINT));
    SmsApiAdmin::returnError($e->getMessage());
}
