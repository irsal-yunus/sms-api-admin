<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;

$logger = Logger::getLogger("service");
$service = new AppJsonService();

try {
    SmsApiAdmin::filterAccess();

    $productId = filter_input(INPUT_POST, 'productId', FILTER_VALIDATE_INT);

    if (empty($productId)) {
        SmsApiAdmin::returnError("Invalid Product ID ($productId) !");
    }

    $page = SmsApiAdmin::getTemplate();

    try {
        $productModel = new InvoiceProduct();
        $product = $productModel->find($productId);

        if (empty($product)) {
            SmsApiAdmin::returnError("Product not found !");
        }

        $product->delete();

        if ($product->isHistory()) {
            $invoiceModel = new InvoiceHistory;

            if ($invoice = $invoiceModel->find($product->ownerId)) {
                $invoice->createInvoiceFile();
            }
        }

        $service->setStatus(true);
        $service->summarise('Product successfully deleted');
        $service->deliver();
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
    SmsApiAdmin::returnError('Interval Server Error');
}
