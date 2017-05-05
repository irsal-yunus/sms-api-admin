<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
try{
	$template = SmsApiAdmin::getLoginManager()->checkIsGuest()?
					'welcome.guest.tpl':
					'welcome.user.tpl';
} catch(Exception $e){
	$template='welcome.guest.tpl';
	$logger->error("$e");
}
$page = SmsApiAdmin::getTemplate();
$page->display($template);