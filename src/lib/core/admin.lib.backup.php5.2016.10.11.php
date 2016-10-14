<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
/**
 * Login manager SMS API Admin application
 */
final class SmsApiAdminLoginManager
	implements ILoginManager{
	/**
	 *
	 * @var int in seconds
	 */
	private static $sessionLifetime=3600;
	private static $sessionUserLabel='_login_user_id';
	private static $sessionTimestampLabel='_login_timestamp';
	/**
	 *
	 * @var SmsApiAdminUser
	 */
	private $user = null;
	/**
	 *
	 * @var Logger
	 */
	private $logger = null;
	/**
	 * constructor
	 */
	public function __construct() {
		$this->logger = Logger::getLogger(__CLASS__);
		$lifeTime = SmsApiAdmin::getConfigValue('app', 'sessionExpiration');
		if($lifeTime !== null){
			if($lifeTime <= 0)
				throw new Exception("Invalid session expiration configuration value ($lifeTime)");
			self::$sessionLifetime = (int) $lifeTime;
		}

	}
	/**
	 * Get current login user
	 * @return SmsApiAdminUser
	 */
	public function getUser() {
		if(!$this->user)
			throw new NotLoggedInException;
		return $this->user;
	}
	/**
	 * Encrypt admin password
	 * It is SHA1 based
	 * @param string $plain Plain text
	 * @return string encrypted
	 */
	private static function encryptPassword($plain){
		return sha1($plain);
	}
	/**
	 * Try log an admin in
	 * Throws exceptions on failure
	 * @param string $username
	 * @param string $password
	 *
	 */
	public function login($username, $password) {
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
			$encPassword = self::encryptPassword($password);
            $query = "select ADMIN_ID, LOGIN_ENABLED=1 as enabled from ADMIN where ADMIN_USERNAME=:username and ADMIN_PASSWORD=:password";
            $stmt = $db->prepare($query);
			$stmt->bindValue(":username", $username, PDO::PARAM_STR);
			$stmt->bindValue(":password", $encPassword, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row){
				throw new FailedLoginException("User not found or incorrect password!");
			}elseif(!$row['enabled']){
				throw new FailedLoginException("User login has been banned!");
			}else{
				$user = new SmsApiAdminUser($row['ADMIN_ID']);
				$this->user = $user;
			}
			$_SESSION[self::$sessionUserLabel] = $row['ADMIN_ID'];
			$_SESSION[self::$sessionTimestampLabel] = time();
		} catch (PDOException $e) {
			$this->logger->error("$e", $this);
			throw new Exception("Query error");
		} catch (LoginException $e) {
			throw $e;
		} catch (Exception $e) {
			$this->logout();
			$this->logger->error("$e");
			throw new FailedLoginException($e->getMessage());
		}
	}
	/**
	 * Log admin out
	 */
	public function logout() {
		try {
			$this->clearSession();
			$this->user = null;
		} catch (Exception $e) {
			$this->logger->error("$e");
			throw new LoginException("Failed logout!");
		}
	}
	
	private function clearSession(){
		SmsApiAdmin::destroySession();
	}

	/**
	 * Resume session
	 */
	public function resume() {
		try {
			if(empty($_SESSION[self::$sessionUserLabel]) 
				|| empty($_SESSION[self::$sessionTimestampLabel]))
				return;//no session
			if($this->isExpired()){
				$this->user = null;
				throw new ExpiredLoginException;
			}
			if($this->user == null){
				$user = new SmsApiAdminUser($_SESSION[self::$sessionUserLabel]);
				if(!$user->mayLogin())
					throw new LoginException('User <'.$user->getName().'> is not permitted to log in');
				$this->user = $user;
			}
			$_SESSION[self::$sessionTimestampLabel] = time();
		} catch (LoginException $e) {
			throw $e;
		} catch (Exception $e) {
			$this->logger->error("$e");
			throw new LoginException('Error resuming user session');
		}
	}
	/**
	 * Has current user logged in
	 * @return bool
	 */
	public function checkIsGuest() {
		return 
			   ($this->user == null)
			|| empty($_SESSION[self::$sessionUserLabel])
			|| $this->isExpired();
	}
	/**
	 * Is current session expired
	 * @return bool
	 */
	private function isExpired() {
		if(empty($_SESSION[self::$sessionTimestampLabel]))
			return true;
		$now = time();
		$last = (int) $_SESSION[self::$sessionTimestampLabel];
		if($now < $last)
			throw Exception("Invalid login time stamp: $last");
		$age = $now - $last;
//		$this->logger->debug("Now = $now, last = $last, age=$age, life=".self::$sessionLifetime.", expired=".($age > self::$sessionLifetime));
		return ($age > self::$sessionLifetime);
	}

}

/**
 * The admin user object class
 */
final class SmsApiAdminUser
	implements IUser{
	/**
	 * Admin ID
	 * @var int
	 */
	private $ID;
	/**
	 * Admin username
	 * @var string
	 */
	private $username;
	/**
	 * Admin real name (for display)
	 * @var string
	 */
	private $displayName;
	/**
	 * Admin login permission
	 * @var bool
	 */
	private $loginPermission=false;
	/**
	 * Admin login permission
	 * @var array
	 */
	private $privileges=array();
	
	/**
	 *
	 * @var Logger
	 */
	private $logger = null;
	
	/**
	 * Constructor
	 * @param int $userID
	 */
	public function __construct($userID) {
		try {
			$this->logger = Logger::getLogger(__CLASS__);
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
			$stmt = $db->prepare("select ADMIN_ID, ADMIN_DISPLAYNAME, ADMIN_USERNAME, LOGIN_ENABLED=1 as loginPermission from ADMIN where ADMIN_ID=:adminID");
			$stmt->bindValue(":adminID", $userID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			if(!$row)
				throw new Exception ("User record was not found");

			$this->ID = $row['ADMIN_ID'];
			$this->username = $row['ADMIN_USERNAME'];
			$this->displayName = $row['ADMIN_DISPLAYNAME'];
			$this->loginPermission = !empty($row['loginPermission']);
			$this->privileges = array();
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("User query error");
		}
	}
	
	/**
	 * Get admin displayname
	 * @return string
	 */
	public function getDisplayName() {
		return $this->displayName;
	}

	/**
	 * Get admin ID
	 * @return in
	 */
	public function getID() {
		return $this->ID;
	}
	/**
	 * Admin username
	 * @return string
	 */
	public function getName() {
		return $this->username;
	}
	/**
	 * May this account used for login
	 * @return bool
	 */
	public function mayLogin() {
		return $this->loginPermission;
	}
	/**
	 * Does this admin has certain privielge
	 * @param string $privilege Privilege name
	 * @return bool
	 */
	public function hasPrivilege($privilege) {
		return in_array($privilege, $this->privileges);
	}
}

/**
 * The Json service reply
 */
class AppJsonService{
	private $success=false;
	private $summary='';
	private $attachment=array();
	/**
	 * Set process status
	 * @param bool $success
	 */
	public function  setStatus($success){
		$this->success = $success && true;
	}
	/**
	 * Summarise report
	 * @param string $summary
	 */
	public function  summarise($summary){
		$this->summary = "$summary";
	}
	/**
	 * Attach data
	 * @param string $label
	 * @param mixed $attachment data
	 */
	public function  attach($label, $attachment){
		if($label===null){
			$this->attachment[] = $attachment;
		}else{
			$this->attachment["$label"] = $attachment;
		}
	}
	/**
	 * Attach prebuild data array
	 * @param mixed $attachment data
	 */
	public function  attachRaw(array $raw){
		$this->attachment = $raw;
	}
	/**
	 * Clear all data
	 */
	public function  clearAttachments(){
		$this->attachment = array();
	}
	/**
	 * DEliver the result
	 */
	public function deliver($terminateApp=true){
		try{
			$product = json_encode(array(
				'success'=>  $this->success,
				'summary'=>  $this->summary,
				'attachment'=>  $this->attachment
			));
			header("Content-type: application/json");
			while(ob_get_level ())
				ob_end_clean ();
			echo $product;
			if($terminateApp)
				exit;
		} catch(Exception $e){
			Logger::getLogger()->error("Failed generating reply: $e");
			if($terminateApp)
				exit;
		}
	}
}

final class SmsApiAdmin {

    const DB_SMSAPI = 'default';
    const DB_COBRANDER = 'cobrander';
    const DB_ALL = 'dball';
    const DB_DUASATU = 'dbas';

	/**
	 *
	 * @var Logger
	 */
	private static $logger;
	/**
	 *
	 * @var ArrayObject
	 */
	private static $config;
	/**
	 *
	 * @var SmsApiAdminLoginManager
	 */
	private static $loginManager=null;

	/**
	 *
	 * @var array
	 */
	private static $db=array();
	
	const SERVICE_TYPE_JSON=1;
	const SERVICE_TYPE_TEXT=2;
	const SERVICE_TYPE_HTML=3;
	private static $mode=self::SERVICE_TYPE_HTML;
	/**
	 * Initialise application environment
	 */
	public static function init(){
		try {
			session_name(SMSAPIADMIN_SESSION_NAME);
			if(session_id()=='')
				session_start();
			Logger::configure(SMSAPIADMIN_CONFIG_DIR.'log4php.ini');
			self::$logger = Logger::getLogger(__CLASS__);
			self::loadConfig('app', true);
			self::loadConfig('database', true);
			self::$loginManager = new SmsApiAdminLoginManager();
			try{
				self::$loginManager->resume();
			} catch(ExpiredLoginException $e){
				self::$logger->debug("Expired login: $e");
			}
		} catch (Exception $e) {
			self::$logger->fatal("Error initialising application: $e");
			exit;
		}
	}
	
	/**
	 * Get configuration
	 * @param string $config The config name, NULL means all
	 * @param bool $autoload Wether to auto load config when not exist
	 * @return array
	 */
	public static function getConfig($config=null, $autoload=true){
		if($config==null) //get all config
			return self::$config;
		if(!isset(self::$config[$config])){
			if(!$autoload)
				return null;
			self::loadConfig($config);
			if(!isset(self::$config[$config])) //failed loading
				return null;
		}
		return self::$config[$config];
	}
	/**
	 * Get property value grom a config
	 * @param string $config
	 * @param string $property
	 * @return mixed property value
	 */
	public static function getConfigValue($config=null, $property=null){
		$properties = self::getConfig($config);
		if(!$properties)
			return null;
		return isset($properties[$property])?
			$properties[$property] : null;
	}
	
	/**
	 * Get a preconfigured database connection
	 * @param string $conn Connection config name
	 * @return PDO
	 */
    public static function getDB($conn) {
        
        if (isset(self::$db[$conn])) {
            $conn = $conn ? self::DB_SMSAPI : self::DB_COBRANDER;
//               self::$db[$conn] = self::$db[$conn]? self::DB_SMSAPI : self::DB_COBRANDER;
            if (!(self::$db[$conn] instanceof PDO))
				throw new RuntimeException('Broken cached database connection');
			return self::$db[$conn];
		}
		if(empty(self::$config['database'][$conn]))
			throw new Exception("Connection specification for '$conn' was not found");
		$config = self::$config['database'][$conn];
		$db = new PDO($config['dsn'], $config['username'], $config['password']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return self::$db[$conn] = $db;
	}

	/**
	 * @return SmsApiAdminLoginManager
	 */
	public static function getLoginManager(){
		return self::$loginManager;
	}
	
	/**
	 * @return SmsApiAdminUser
	 */
	public static function getCurrentUser(){
		return self::$loginManager->getUser();
	}

	/**
	 * Get a page template
	 * @return Smarty
	 */
	public static function getTemplate(){
		$template = new Smarty();
		$template->setTemplateDir(SMSAPIADMIN_TEMPLATE_DIR);
//		$template->cache_dir = SMSAPIADMIN_TEMPLATE_CACHE_DIR;
		$template->compile_dir = SMSAPIADMIN_TEMPLATE_COMPILE_DIR;
		return $template;
	}

	/**
	 * Load configuration
	 * @param mixed $config string of config section or null for global config
	 * @param bool $reload
	 * @return array
	 */
	public static function loadConfig($config, $reload=false){
		if(!$reload && isset(self::$config[$config]))
			return self::$config[$config];
		$filePath = SMSAPIADMIN_CONFIG_DIR.$config.'.ini';
		if(!is_readable($filePath))
			throw new Exception("Unreadable configuration file: $config");
		$rules = parse_ini_file($filePath, true);
		if($rules === false)
			throw new Exception("Configuration error in '$config'");
		self::$config[$config] = $rules;
	}
	/**
	 * Return error message and terminate application
	 * @param string $message
	 */
	public static function returnError($message=''){
		try {
			switch(self::$mode){
				case self::SERVICE_TYPE_JSON:
					$service = new AppJsonService();
					$service->setStatus(false);
					$service->summarise($message);
					$service->deliver(true);
				break;
				case self::SERVICE_TYPE_TEXT:
					echo $message;
				break;
				case self::SERVICE_TYPE_HTML:
					$page = self::getTemplate();
					$page->assign('message', $message);
					$page->display('error.tpl');
				break;
				default:
					echo $message;
					break;
			}
			exit;
		} catch (Exception $e) {
			self::$logger->fatal("$e");
			exit;
		}
	}
	/**
	 * Filter user access
	 * @param strin $privilege requiired privelge naem if FALSE then it only check login status
	 * @param mixed $termination
	 *              FALSE: only return status;
	 *              <string>: redirect;
	 *              other: diplay error message;
	 * @return <type> 
	 */
	public static function filterAccess($privilege=false, $termination=true){
		try {
			if(!self::$loginManager->checkIsGuest()){
				if(($privilege===false) || (self::getCurrentUser()->hasPrivilege($privilege)))
					return true;
			}
			if($termination===false)
				return false;

			if(is_string($termination) && !empty($termination)){
				header('Location: '.  addslashes($termination));
				exit;
			}
			
			self::returnError('Unauthorised access!', self::$mode);
			exit;
		} catch (Exception $e) {
			self::$logger->fatal("Error filtering access: $e");
			exit;
		}
	}
	/**
	 * Set application mode
	 * @param int $mode application mode SmsApiAdmin::SERVICE_TYPE_*
	 */
	public static function setServiceMode($mode){
		try {
			switch($mode){
				case self::SERVICE_TYPE_JSON:
				case self::SERVICE_TYPE_TEXT:
				case self::SERVICE_TYPE_HTML:
					self::$mode = $mode;
					break;
				default:
					throw new Exception('Invalid service mode');
					break;
			}
		} catch (Exception $e) {
			self::$logger->error("$e");
			throw $e;
		}
	}
	/**
	 * Exception heandelr
	 * @param Exception $e
	 */
	public static function catchException(Exception $e) {
		try {
			if($e instanceof ErrorException){
				switch ($e->getSeverity()){
					case E_WARNING :
					case E_USER_WARNING :
					case E_CORE_WARNING :
					case E_COMPILE_WARNING :
					case E_RECOVERABLE_ERROR :
						Logger::getRootLogger()->warn("$e");
						return;
					case E_STRICT :
					case E_DEPRECATED :
					case E_USER_DEPRECATED :
					case E_NOTICE :
					case E_USER_NOTICE :
					case E_CORE_WARNING :
					case E_COMPILE_WARNING :
					case E_USER_WARNING :
						Logger::getRootLogger()->info("$e");
						return;
				}
			}
			Logger::getRootLogger()->error("$e");
			self::returnError($e->getMessage());
		} catch (Exception $e) {
			error_log("Uncaught exception: $e");
			exit;
		}
	}
	public static function catchError($errno, $errstr, $errfile, $errline) {
		try {
			self::catchException(new ErrorException($errstr, 0, $errno, $errfile, $errline));
			return true;
		} catch (Exception $e) {
			error_log("Uncaught exception: $e");
			return true;
		}
	}
	public static function destroySession(){
		$_SESSION = array();
		$sessionName = session_name();
		$sessionCookie = session_get_cookie_params();
		session_destroy();
		if (isset($_COOKIE[$sessionName])) {
			$past = time() - 3600;
			if ((empty($sessionCookie['domain'])) && (empty($sessionCookie['secure']))) {
				$unset = setcookie($sessionName, '', $past, $sessionCookie['path']);
			} elseif (empty($sessionCookie['secure'])) {
				$unset = setcookie($sessionName, '', $past, $sessionCookie['path'], $sessionCookie['domain']);
			} else {
				$unset = setcookie($sessionName, '', $past, $sessionCookie['path'], $sessionCookie['domain'], $sessionCookie['secure']);
			}
		}
		session_name(SMSAPIADMIN_SESSION_NAME);
		session_start();
		session_regenerate_id();
	}

	public static function redirectUrl($url){
		$baseUrl = SMSAPIADMIN_BASE_URL;
		$isAbsolute = filter_var($url, FILTER_VALIDATE_URL, array('options'=>array('flags'=>FILTER_FLAG_SCHEME_REQUIRED)));
		if($isAbsolute){
			$cleanUrl = addslashes($url);
			header("Location: $cleanUrl");
			exit;
		}else{
			$cleanUrl = urlencode($url);
			header('Location: '.SMSAPIADMIN_BASE_URL.$cleanUrl);
			exit;
		}		
	}

}
