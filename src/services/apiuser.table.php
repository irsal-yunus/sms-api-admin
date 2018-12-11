<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';
SmsApiAdmin::filterAccess();
$page = SmsApiAdmin::getTemplate();
$apiuserManager = new ApiUser();
$apiclientManager = new ApiBusinessClient();
try {
	$optionDefinitions =array(
		'onlyActiveUser'=>array(
			'filter'=>FILTER_VALIDATE_BOOLEAN,
			'flags'=>FILTER_NULL_ON_FAILURE
		),
		'clientID'=>array(
			'filter'=>FILTER_VALIDATE_INT,
			'options'=>array('min_range'=>1)
		),
		'onlySpecifiedClient'=>array(
			'filter'=>FILTER_VALIDATE_BOOLEAN
		),
		'highlight'=>array(
			'filter'=>FILTER_VALIDATE_INT,
			'options'=>array('min_range'=>1)
		)
	);
	$filters = array(
		'active'=>1,
	);
	$options = array(
		'onlyActiveUser'=>true,
		'onlySpecifiedClient'=>false,
		'highlight'=>null
	);
	$allOptions = filter_input_array(INPUT_POST, $optionDefinitions);
//	$logger->debug(print_r($allOptions, 1));
	if($allOptions) foreach($allOptions as $optionName=>$optionValue){
		if($optionValue===null)
			continue;
		$options[$optionName] = $optionValue;
		switch($optionName){
			case 'onlyActiveUser':
				if($optionValue){
					$filters['active'] = 1;
				}elseif(isset($filters['active'])){
					unset($filters['active']);
				}
			break;
			case 'clientID':
				if($optionValue===false)
					continue;
				$filters[$optionName]=$optionValue;
				break;
		}
	}
	if($filters){
		$users = $apiuserManager->findAll($filters);
	}else{
		$users = $apiuserManager->getAll();
	}
	if($options['onlySpecifiedClient']){
		require_once SMSAPIADMIN_LIB_DIR.'model/ApiBusinessClient.php';
		$clientManager = new ApiBusinessClient();
		$page->assign('client', $clientManager->getDetails($clientID));
	}

	$client = $apiclientManager->getDetails($options['clientID']);
	$activeClient = $client['archivedDate'] === null

	$page->assign('activeClient', $activeClient);
	$page->assign('options', $options);
	$page->assign('optionsJson', json_encode($options));
	$page->assign('users', $users);
	$page->display('apiuser.table.tpl');
} catch (Exception $e) {
	SmsApiAdmin::returnError($e->getMessage());
}