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
		'highlight'=>array(
			'filter'=>FILTER_VALIDATE_INT,
			'options'=>array('min_range'=>1)
		)
	);
	$filters = array(
	);
	$options = array(
		'highlight'=>null
	);
	$allOptions = filter_input_array(INPUT_POST, $optionDefinitions);
	if($allOptions) {
		foreach($allOptions as $optionName=>$optionValue){
			if($optionValue===null)
				continue;
			$options[$optionName] = $optionValue;
			switch($optionName){
//				case 'onlyActiveUser':
//					if($optionValue){
//						$filters['active'] = 1;
//					}elseif(isset($filters['active'])){
//						unset($filters['active']);
//					}
//				break;
			}//end switch
		}//end foreach
	}
//	if($filters){
//		$clients = $clientManager->getAll();
//	}else{
		$clients = $clientManager->getAll();
//	}


	$page->assign('options', $options);
	$page->assign('optionsJson', json_encode($options));
	$page->assign('clients', $clients);
	$page->display('client.table.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}