<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';

SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
$clientManager = new ApiBusinessClient();
try {
    $optionDefinitions =array(
        'onlyUnarchived'=>array(
            'filter'=>FILTER_VALIDATE_BOOLEAN,
            'flags'=>FILTER_NULL_ON_FAILURE
        ),
        'highlight'=>array(
            'filter'=>FILTER_VALIDATE_INT,
            'options'=>array('min_range'=>1)
        )
    );
    $filters = array(
    );

    $flag = 0 ;
    $options = array(
        'onlyUnarchived'=>true,
        'highlight'=>null,
    );

    $allOptions = filter_input_array(INPUT_POST, $optionDefinitions);
    if($allOptions) {
        foreach($allOptions as $optionName=>$optionValue){
            if($optionValue===null)
                continue;
            $options[$optionName] = $optionValue;
            switch($optionName){
                case 'onlyUnarchived':
                    if($optionValue) $flag = 0;
                    else $flag = 1;
                break;
            }
        }
    }

    $clients = ($flag===0 ? $clientManager->getOnlyUnarchivedClient() : $clientManager->getAll() );

    $page->assign('options', $options);
    $page->assign('optionsJson', json_encode($options));
    $page->assign('clients', $clients);
    $page->display('client.table.tpl');
} catch (Exception $e) {
    SmsApiAdmin::returnError($e->getMessage());
}