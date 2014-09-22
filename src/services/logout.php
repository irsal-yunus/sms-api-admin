<?php
require_once '../init.d/init.service.php';

$logger = Logger::getLogger('service/logout');
try {
	$login = SmsApiAdmin::getLoginManager();
	$service = new AppJsonService();
//	$logger->debug("Session: ".print_r($_SESSION, true));
	if($login->checkIsGuest()){
		$service->setStatus(false);
		$service->summarise('Can not log not-logged-in user out!');
		$service->deliver();
	}
	$login->logout();
	$service->setStatus(true);
	$service->summarise('User has been logged out');
	$service->deliver();
} catch (LoginException $e) {
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
	$login->logout();
} catch (Exception $e) {
	session_destroy();
	$service->setStatus(false);
	$service->summarise('Application error');
	$service->deliver();
}
