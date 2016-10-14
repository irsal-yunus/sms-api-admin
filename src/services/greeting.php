<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
$page = SmsApiAdmin::getTemplate();
try{
	if(!SmsApiAdmin::getLoginManager()->checkIsGuest()){
		$template = 'greeting.user.tpl';
		$user = SmsApiAdmin::getCurrentUser();
		$page->assign('welcomeName', $user->getDisplayName());
	}else{
		$template = 'greeting.guest.tpl';
	}
} catch(Exception $e){
	$template='greeting.guest.tpl';
	$logger->error("$e");
}
$page->display($template);