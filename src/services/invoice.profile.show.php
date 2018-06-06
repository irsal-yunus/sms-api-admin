<?php

use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;

/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

$logger = Logger::getLogger("service");
try {
    SmsApiAdmin::filterAccess();
    $page = SmsApiAdmin::getTemplate();
    $profileId = filter_input(INPUT_POST, 'profileId', FILTER_VALIDATE_INT);

    if (empty($profileId)) {
        SmsApiAdmin::returnError("Invalid Invoice Profile ID ($profileId) !");
    }

    try {
        $model = new InvoiceProfile();

        $profile = $model->withProduct($profileId);

        if (empty($profile)) {
            SmsApiAdmin::returnError("Invoice Profile not found !");
        }

        $apiUsers = $profile[0]->loadApiUsers();

        if (is_array($apiUsers)) {
            // var_dump(array_column($apiUsers, 'userId'));
            // $apiUsers = implode(', ', array_column($apiUsers, 'userName','userId'));
             $apiUsers = array_column($apiUsers, 'userName','userId');
        }

        $page->assign('profile', $profile[0]);
        $page->assign('apiUsers', $apiUsers);
        $page->display('invoice.profile.show.tpl');
    } catch (Exception $e) {
        SmsApiAdmin::returnError($e->getMessage());
    }
} catch (Exception $e) {
    $logger->error("$e");
}
