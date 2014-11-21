<?php
/* 
 * Copyright(c) 2014 1rstWAP. All rights reserved.
 */

/**
 * Description of ApiCobranderData
 *
 * @author ferri
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR . "model/ApiCobranderData.php";
SmsApiAdmin::filterAccess();
$pageCobrander = SmsApiAdmin::getTemplate();
try {
    $model = new ApiCobranderData();
    $datas = $model->getCobranderData();
    $pageCobrander->assign('datas',$datas);
    $pageCobrander->display('apiuser.selectCobranderId.tpl');
} catch (Exception $exc) {
    SmsApiAdmin::returnError($exc->getMessage());
}
