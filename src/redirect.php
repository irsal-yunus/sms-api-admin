<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 *
 * Redirect all request
 * only for internal use, so user must have logged in
 */

require_once 'init.d/init.php';
SmsApiAdmin::filterAccess();

$url = isset($_GET['url'])? $_GET['url'] : null;
$sanitisedUrl = filter_var($url, FILTER_SANITIZE_STRING, array('flags'=>FILTER_FLAG_STRIP_LOW));
if(!$sanitisedUrl){
	$logger = Logger::getRootLogger();
	$logger->error("Invalid destination URL: $url");
	$sanitisedUrl = 'index.php';
}

SmsApiAdmin::redirectUrl($sanitisedUrl);
