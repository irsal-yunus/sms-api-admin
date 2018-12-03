<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();
    $invoiceId = filter_input(INPUT_POST, 'invoiceId', FILTER_VALIDATE_INT);

    if (empty($invoiceId)) {
        SmsApiAdmin::returnError("Invalid Invoice ID ($invoiceId) !");
    }

    try {

        $historyModel = new InvoiceHistory();
        $profileModel = new InvoiceProfile();
        $settingModel = new InvoiceSetting();
        $minimumCommitment = null;
        $history = $historyModel->withProduct($invoiceId);

        if (empty($history)) {
            SmsApiAdmin::returnError("Invoice not found !");
        }
        $history = $history[0];
        $profile = $history->getProfile();

        if ($profile['useMinCommitment'] == 1) {
            $history->minimumCommitment($profile);
        }

        $page->assign('profile', $profile);
        $page->assign('invoice', $history);
        $page->assign('setting', $settingModel->getSetting());
        $page->display('invoice.history.show.tpl');

    } catch (Exception $e) {
        $logger->error($e->getTraceAsString());
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
