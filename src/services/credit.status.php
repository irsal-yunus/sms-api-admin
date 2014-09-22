<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once '../init.d/init.service.php';

try {
	$service = new AppJsonService();
	try {

	} catch (Exception $e) {
		trigger_error("Failed processin request: $e");
		$service->setStatus(false);
		$service->summarise($e->getMessage());
		$service->deliver();
	}
	$service->deliver();
} catch (Exception $e) {
	trigger_error("Failed generating reply: $e");
}