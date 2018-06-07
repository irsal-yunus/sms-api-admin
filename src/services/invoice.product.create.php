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

        if ($data['ownerType'] === InvoiceProduct::HISTORY_PRODUCT) {
            $dateRange = [];
            $begin = new DateTime("now");
            $end = new DateTime("-10 months");

            for ($i = $begin; $i >= $end; $i->modify('-1 month')) {
                $dateRange[$i->format('Y-m-t')] = $i->format("F Y");
            }

            $page->assign('selectedRange', date('Y-m-t', strtotime('-1month')));
            $page->assign('dateRange', $dateRange);

            $history = new InvoiceHistory();
            if (!$history = $history->find($data['ownerId'])) {
                SmsApiAdmin::returnError("Invoice not found");
            }
            $profileId = $history->profileId;
        } else {
            $profileId = $data['ownerId'];
        }

        $profileModel = new InvoiceProfile();

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
        $page->assign('owner', $data);
        $page->display('invoice.product.create.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
