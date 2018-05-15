<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once dirname(__DIR__).'/vendor/autoload.php';

$page = SmsApiAdmin::getTemplate();
$login = SmsApiAdmin::getLoginManager();
$page->assign('siteTitle', SmsApiAdmin::getConfigValue('app', 'siteTitle'));
if($login->checkIsGuest()){
	$isLogin = false;
	$welcomeName = 'Guest';
	$page->assign('isLogin', false);
}else{
	$page->assign('isLogin', true);
	$page->assign('welcomeName', $login->getUser()->getDisplayName());
}
$page->display('index.tpl');

