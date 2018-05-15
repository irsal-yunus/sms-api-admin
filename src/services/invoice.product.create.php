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

    $definitions = [
        "ownerType" => FILTER_SANITIZE_STRING,
        "ownerId" => FILTER_SANITIZE_NUMBER_INT,
    ];

    $data = filter_input_array(INPUT_POST, $definitions);

    if (empty($data['ownerId'])) {
        SmsApiAdmin::returnError("Owner Id cannot empty");
    }

    if (empty($data['ownerType'])) {
        SmsApiAdmin::returnError("Owner Type cannot empty");
    } else if (!in_array($data['ownerType'], [InvoiceProduct::PROFILE_PRODUCT, InvoiceProduct::HISTORY_PRODUCT])) {
        SmsApiAdmin::returnError("Invalid Owner Type value");
    }

    try {
        $results = [];
        $apiReport = new ApiReport();

        // $reports = $product->getReports();
        $groups = $apiReport->getBillingReportGroup();

        if (!empty($groups)) {
            $results['GROUPS'] = array_combine($groups, $groups);
        }

        $reports = $apiReport->getBillingReport();

        if (!empty($reports)) {
            $results['REPORTS'] = array_combine($reports, $reports);
        }

        $page->assign('reports', $results);
        $page->assign('owner', $data);
        $page->display('invoice.product.create.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
