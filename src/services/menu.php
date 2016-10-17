<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.php';
try{
	$templateFile = SmsApiAdmin::getLoginManager()->checkIsGuest()?
					'menu.forGuest.tpl':
					'menu.forUser.tpl';
} catch(Exception $e){
	$templateFile='menu.forGuest.tpl';
	$logger->error("$e");
}
$template = SmsApiAdmin::getTemplate();
$template->display($templateFile);