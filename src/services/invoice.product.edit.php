<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiReport.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();

    $productId = filter_input(INPUT_POST, 'productId', FILTER_VALIDATE_INT);

    if (empty($productId)) {
        SmsApiAdmin::returnError("Invalid Product ID ($productId) !");
    }

    try {

        $productModel = new InvoiceProduct();

        if(!$product = $productModel->find($productId)) {
            SmsApiAdmin::returnError("Product Not Found");
        }

        $results = [];
        $apiReport = new ApiReport();

        $groups = $apiReport->getBillingReportGroup();

        if (!empty($groups)) {
            $results['GROUPS'] = array_combine($groups, $groups);
        }

        $reports = $apiReport->getBillingReport();

        if (!empty($reports)) {
            $results['REPORTS'] = array_combine($reports, $reports);
        }

        $page->assign('reports', $results);
        $page->assign('product', $product);
        $page->display('invoice.product.edit.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
