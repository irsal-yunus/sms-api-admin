<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';
require_once SMSAPIADMIN_LIB_DIR . 'model/ApiReport.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

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
        $profileModel = new InvoiceProfile();

        if(!$product = $productModel->find($productId)) {
            SmsApiAdmin::returnError("Product Not Found");
        }

        $results = [];
        $apiReport = new ApiReport();

        if ($product->ownerType === InvoiceProduct::HISTORY_PRODUCT) {
            $dateRange = [];
            $begin = new DateTime("now");
            $end   = new DateTime("-10 months");
            for ($i = $begin; $i >= $end; $i->modify('-1 month')) {
                $dateRange[$i->format('Y-m-t')] = $i->format("F Y");
            }
            $page->assign('selectedRange', date('Y-m-t', strtotime($product->period)));
            $page->assign('dateRange', $dateRange);
            $page->assign('realDate',$product->period);
            $history = new InvoiceHistory();
            if (!$history = $history->find($product->ownerId)) {
                SmsApiAdmin::returnError("Invoice not found");
            }
            $profileId = $history->profileId;
        } else {
            $profileId = $product->ownerId;
        }

        if (!$profile = $profileModel->find($profileId)) {
            SmsApiAdmin::returnError("Profile not found");
        }

        $groups = $apiReport->getBillingReportGroup($profile->clientId);

        if (!empty($groups)) {
            $results['GROUPS'] = array_combine($groups, $groups);
        }

        $reports = $apiReport->getBillingReport($profile->clientId);

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
