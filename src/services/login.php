<?php
require_once '../init.d/init.service.php';

$logger = Logger::getLogger('service/login');
try {
	$login = SmsApiAdmin::getLoginManager();
	$service = new AppJsonService();
//	$logger->debug("Session: ".print_r($_SESSION, true));
	if(!$login->checkIsGuest()){
		$service->setStatus(false);
		$service->summarise('Can not relogin while logged in!');
		$service->deliver();
	}
	if(!isset($_POST['username']) || !isset($_POST['password']) ){
		$service->setStatus(false);
		$service->summarise('Missing required parameters!');
		$service->deliver();
	}
	$username = $_POST['username'];
	$password = $_POST['password'];
	$login->login($username, $password);
	$user = $login->getUser();
	$service->setStatus(true);
	$service->summarise('Login success!');
	$service->attach('userID', $user->getID());
	$service->attach('userDisplayName', $user->getDisplayName());
	$service->deliver();
} catch (LoginException $e) {
	$service->setStatus(false);
	$service->summarise($e->getMessage());
	$service->deliver();
} catch (Exception $e) {
	$logger->error("$e");
	$service->setStatus(false);
	$service->summarise('Application error');
	$service->deliver();
}