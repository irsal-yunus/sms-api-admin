<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

$page = SmsApiAdmin::getTemplate();
if (isset($_POST['action'])) {
    $page->assign('action', $_POST['action']);
    $page->assign('message', $_POST['message']);
} else {
    throw new InvalidArgumentException('Missing userID from arguments');
}
$page->display('billing.deleteBillingProfileForm.tpl');
