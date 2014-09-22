<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

/**
 * Description of ILoginManager
 *
 * @author setia.budi
 */
interface ILoginManager {
	public function login($user, $pass);
	public function logout();
	public function resume();
	public function checkIsGuest();
	/**
	 * @return IUser
	 */
	public function getUser();
}

class LoginException extends Exception {
	public function __construct($message, $code=0, $previous=null) {
		parent::__construct($message, $code/*, $previous Not in php5.2*/);
	}
}
class FailedLoginException extends LoginException {
	public function __construct($message, $code=0, $previous=null) {
		parent::__construct($message, $code, $previous);
	}
}
class ExpiredLoginException extends LoginException {
	public function __construct($message='Session has expired', $code=0, $previous=null) {
		parent::__construct($message, $code, $previous);
	}
}
class NotLoggedInException extends LoginException {
	public function __construct($message='User was not logged in', $code=0, $previous=null) {
		parent::__construct($message, $code, $previous);
	}
}