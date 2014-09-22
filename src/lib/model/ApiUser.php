<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */


/**
 * Description of ApiUser
 *
 * @author setia.budi
 */
class ApiUser extends ApiBaseModel{
	/**
	 *
	 * @var array
	 */
	private $userConfig;
	public function __construct() {
		parent::__construct();
		$this->userConfig = SmsApiAdmin::getConfig('user');
	}
	/**
	 *
	 * @return array
	 */
	public function  getAll() {
		try {
			$query = "select
					u.USER_ID as userID,
					u.CLIENT_ID as clientID,
					c.COMPANY_NAME as clientCompanyName,
					u.USER_NAME as userName,
					(u.ACTIVE=1) as active,
					if(u.ACTIVE,'Active','Inactive') as statusName,
					u.CREDIT as userCredit,
					u.COBRANDER_ID as cobranderID,
					u.DELIVERY_STATUS_URL as statusDeliveryUrl,
					u.URL_INVALID_COUNT as statusDeliveryUrlInvalidCount,
					(u.URL_ACTIVE=1) as statusDeliveryActive,
					u.URL_LAST_RETRY as statusDeliveryUrlLastRetry,
                                        u.EXPIRED_DATE as expiredDate,
					(u.USE_BLACKLIST=1) as replyBlacklistEnabled,
					if(u.USE_BLACKLIST=1,'Enabled','Disabled') as replyBlacklistStatusName,
					(u.IS_POSTPAID=1) as isPostpaid,
					if(u.IS_POSTPAID=1,'Yes','No') as isPostpaidStatusName,
					u.LAST_ACCESS as lastAccess,
					u.CREATED_DATE as createdTimestamp,
					u.CREATED_BY as createdTimestamp,
					a1.ADMIN_DISPLAYNAME as createdByName,
					u.UPDATED_DATE as updatedTimestamp,
					u.UPDATED_BY as updatedBy,
					a2.ADMIN_DISPLAYNAME as updatedByName
				from USER as u
					inner join CLIENT as c on u.CLIENT_ID = c.CLIENT_ID
					inner join ADMIN as a1 on u.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on u.UPDATED_BY=a2.ADMIN_ID
				order by u.USER_NAME asc";
			return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	public function  getAllClientUsers($clientID) {
		try {
			$query = "select
					u.USER_ID as userID,
					u.CLIENT_ID as clientID,
					c.COMPANY_NAME as clientCompanyName,
					u.USER_NAME as userName,
					(u.ACTIVE=1) as active,
					if(u.ACTIVE,'Active','Inactive') as statusName,
					u.CREDIT as userCredit,
					u.COBRANDER_ID as cobranderID,
					u.DELIVERY_STATUS_URL as statusDeliveryUrl,
					u.URL_INVALID_COUNT as statusDeliveryUrlInvalidCount,
					(u.URL_ACTIVE=1) as statusDeliveryActive,
					u.URL_LAST_RETRY as statusDeliveryUrlLastRetry,
					u.EXPIRED_DATE as expiredDate,
					(u.USE_BLACKLIST=1) as replyBlacklistEnabled,
					if(u.USE_BLACKLIST=1,'Enabled','Disabled') as replyBlacklistStatusName,
					(u.IS_POSTPAID=1) as isPostpaid,
					if(u.IS_POSTPAID=1,'Yes','No') as isPostpaidStatusName,
					u.LAST_ACCESS as lastAccess,
					u.CREATED_DATE as createdTimestamp,
					u.CREATED_BY as createdTimestamp,
					a1.ADMIN_DISPLAYNAME as createdByName,
					u.UPDATED_DATE as updatedTimestamp,
					u.UPDATED_BY as updatedBy,
					a2.ADMIN_DISPLAYNAME as updatedByName
				from USER as u
					inner join CLIENT as c on u.CLIENT_ID = c.CLIENT_ID
					inner join ADMIN as a1 on u.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on u.UPDATED_BY=a2.ADMIN_ID
				where u.CLIENT_ID=:clientID
				order by u.USER_NAME asc";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
			if(!$stmt->execute())
				throw new Exception('Query error!');
			$stmt->execute();
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			unset($stmt);
			return $list;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception('Query error');
		}
	}
	public function  findAll(array $filters) {
		try {
			$query = "select
					u.USER_ID as userID,
					u.CLIENT_ID as clientID,
					c.COMPANY_NAME as clientCompanyName,
					u.USER_NAME as userName,
					(u.ACTIVE=1) as active,
					if(u.ACTIVE,'Active','Inactive') as statusName,
					u.CREDIT as userCredit,
					u.COBRANDER_ID as cobranderID,
					u.DELIVERY_STATUS_URL as statusDeliveryUrl,
					u.URL_INVALID_COUNT as statusDeliveryUrlInvalidCount,
					(u.URL_ACTIVE=1) as statusDeliveryActive,
					u.URL_LAST_RETRY as statusDeliveryUrlLastRetry,
                                        u.EXPIRED_DATE as expiredDate,
					(u.USE_BLACKLIST=1) as replyBlacklistEnabled,
					if(u.USE_BLACKLIST=1,'Enabled','Disabled') as replyBlacklistStatusName,
					(u.IS_POSTPAID=1) as isPostpaid,
					if(u.IS_POSTPAID=1,'Yes','No') as isPostpaidStatusName,
					u.LAST_ACCESS as lastAccess,
					u.CREATED_DATE as createdTimestamp,
					u.CREATED_BY as createdTimestamp,
					a1.ADMIN_DISPLAYNAME as createdByName,
					u.UPDATED_DATE as updatedTimestamp,
					u.UPDATED_BY as updatedBy,
					a2.ADMIN_DISPLAYNAME as updatedByName
				from USER as u
					inner join CLIENT as c on u.CLIENT_ID = c.CLIENT_ID
					inner join ADMIN as a1 on u.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on u.UPDATED_BY=a2.ADMIN_ID";
			$whereClause = false;
			if($filters){
				$filterRules = array(
					'active'=>array('param'=>':active', 'field'=>'u.ACTIVE', 'type'=>PDO::PARAM_BOOL),
					'clientID'=>array('param'=>':clientID', 'field'=>'u.CLIENT_ID', 'type'=>PDO::PARAM_INT)
				);
				$whereClause = self::buildDynamicWhereClause($filterRules, $filters, true);
				$query .= $whereClause;
			}
			$query.=" order by u.USER_NAME asc";
			if($whereClause){
				$stmt  = $this->db->prepare($query);
				self::bindDynamicValues($filterRules, $filters, $stmt);
				$stmt->execute();
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}else{
				return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
			}			
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}


	public function  register($data) {
		try {
			$query = 'insert into USER (
							USER_NAME, PASSWORD, CLIENT_ID,EXPIRED_DATE, ACTIVE,
							COBRANDER_ID, USE_BLACKLIST, IS_POSTPAID,
							CREATED_BY, CREATED_DATE,
							CREDIT, LAST_ACCESS, version, COUNTER,
							DELIVERY_STATUS_URL, URL_ACTIVE , URL_LAST_RETRY, URL_INVALID_COUNT)
						values(
							:userName, :userPassword, :clientID, :expiredDate, :active,
							:cobranderID, :replyBlacklistEnabled, :isPostpaid,
							:adminID, now(),
							0, null, 0, null,
							:statusDeliveryUrl,';
			if(empty($data['clientID']))
				throw new InvalidArgumentException("Missing clientID");
			if(empty($data['userName']))
				throw new InvalidArgumentException("Missing username");
			$this->validatePasswordString(isset($data['userPassword'])? $data['userPassword'] : '');
			if(filter_var($data['statusDeliveryActive'], FILTER_VALIDATE_BOOLEAN)){
				$deliveryStatusFieldType = PDO::PARAM_STR;
				$query .= 'null, 0, 1)';
			}else{
				$deliveryStatusFieldType=PDO::PARAM_NULL;
				$data['statusDeliveryUrl'] = null;
				$query .= 'null, null, null)';
			}
			$adminID = SmsApiAdmin::getCurrentUser()->getID();
			if(!$this->checkUsernameAvailability($data['userName']))
				throw new Exception("Username '{$data['userName']}' exists");
			$data['replyBlacklistEnabled'] = (isset($data['replyBlacklistEnabled'])
											&& $data['replyBlacklistEnabled'] > 0)?
												1 : 0;
			$data['isPostpaid'] = (isset($data['isPostpaid'])
											&& $data['isPostpaid'] > 0)?
												1 : 0;
                        error_log($query);
                        
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userName', $data['userName'], PDO::PARAM_STR);
			$stmt->bindValue(':userPassword', $this->encryptPassword($data['userPassword']), PDO::PARAM_STR);
			$stmt->bindValue(':active', !empty($data['active']), PDO::PARAM_BOOL);
			$stmt->bindValue(':cobranderID', $data['cobranderID'], PDO::PARAM_STR);
			$stmt->bindValue(':expiredDate', $data['expiredDate'], PDO::PARAM_STR);
			$stmt->bindValue(':replyBlacklistEnabled', $data['replyBlacklistEnabled'], PDO::PARAM_INT);
			$stmt->bindValue(':isPostpaid', $data['isPostpaid'], PDO::PARAM_INT);
			$stmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
			$stmt->bindValue(':clientID', $data['clientID'], PDO::PARAM_INT);
			$stmt->bindValue(':statusDeliveryUrl', $data['statusDeliveryUrl'], $deliveryStatusFieldType);
			$stmt->execute();
			$userID = $this->db->lastInsertId();
			if(!$userID)
				throw new RuntimeException('Failed getting newly inserted user ID');
			return $userID;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}

	protected function encryptPassword($string){
		return md5($string);
	}
	
	protected function validatePasswordString($string){
		$string = (string) $string;
		if($string === '')
			throw new Exception('Password can not be empty');
	}
	
	public function validateUserPassword($userID, $password){
		try {
			$query = 'select count(*) from USER where USER_ID=:userID and PASSWORD=md5(:oldPassword)';
			$pwdStmt = $this->db->prepare($query);
			$pwdStmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$pwdStmt->bindValue(':oldPassword', $userID, PDO::PARAM_STR);
			$pwdStmt->execute();
			$correctPassword = $pwdStmt->fetchColumn(0) > 0;
			return $correctPassword;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	
	public function  changePassword($userID, $password) {
		try{
			if(!$this->checkExistence($userID))
				throw new Exception('Attempt to update a nonexisting API user account, ID='.$userID);
			$this->validatePasswordString($password);
			$stmt = $this->db->prepare('update USER set PASSWORD=?, UPDATED_BY=?, UPDATED_DATE=now() where USER_ID=?');
			$stmt->bindValue(1, $this->encryptPassword($password), PDO::PARAM_STR);
			$stmt->bindValue(2, SmsApiAdmin::getCurrentUser()->getID(), PDO::PARAM_INT);
			$stmt->bindValue(3, $userID, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw  new Exception("Query error");
		}
	}
	public function  getUserSenderID($userID) {
		try {
			if(empty ($userID))
				throw new Exception("Invalid UserID");
			$query = "select
						SENDER_ID  as senderID,
						SENDER_NAME  as senderName,
						RANGE_START  as rangeStart,
						RANGE_END  as rangeEnd,
						SENDER_ENABLED = 1  as senderEnabled,
						IF(SENDER_ENABLED, 'Enabled', 'Disabled')  as senderStatusName
					from SENDER
					where USER_ID=:userID ";

			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->execute();
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $list? $list : array();
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	
	public function  update($userID, $data) {
		try {
			$this->checkExistence($userID, true);
			if(empty ($data))
				throw new Exception("Empty data");
			$rules = array(
				'userName'=>array('param'=>':userName', 'field'=>'USER_NAME', 'type'=>PDO::PARAM_STR),
				'expiredDate'=>array('param'=>':expiredDate', 'field'=>'EXPIRED_DATE', 'type'=>PDO::PARAM_STR),
				'cobranderID'=>array('param'=>':cobranderID', 'field'=>'COBRANDER_ID', 'type'=>PDO::PARAM_INT),
				'replyBlacklistEnabled'=>array('param'=>':replyBlacklistEnabled', 'field'=>'USE_BLACKLIST', 'type'=>PDO::PARAM_INT),
				'isPostpaid'=>array('param'=>':isPostpaid', 'field'=>'IS_POSTPAID', 'type'=>PDO::PARAM_INT),
				'statusDeliveryActive'=>array('param'=>':statusDeliveryActive', 'field'=>'URL_ACTIVE', 'type'=>PDO::PARAM_INT),
				'statusDeliveryUrl'=>array('param'=>':statusDeliveryUrl', 'field'=>'DELIVERY_STATUS_URL', 'type'=>PDO::PARAM_STR, 'emptyValue'=>null, 'emptyType'=>PDO::PARAM_NULL)
			);
			if(isset($data['userName'])){
				if($this->checkIsUserNameModified($data['userName'], $userID)){
					if(!$this->checkUsernameAvailability($data['userName'])){
						throw new Exception("User name '{$data['userName']}' exists");
					}
				}else{
					unset($data['userName']);//do not include this
				}
			}
			$queryFields = parent::buildDynamicUpdateClause($rules, $data);
			if(!$queryFields)
				throw new Exception("No update field");
			if(isset($data['statusDeliveryActive'])){
				$data['statusDeliveryActive'] = $data['statusDeliveryActive']? 1 : 0;
				if(!isset($data['statusDeliveryUrl'])){
					$queryFields.=', DELIVERY_STATUS_URL=null';
				}else{
					$data['statusDeliveryUrl'] = trim($data['statusDeliveryUrl']);
				}
				$queryFields.=', URL_LAST_RETRY=null, URL_INVALID_COUNT=0';
			}
			$query = "update USER set UPDATED_BY=:adminID, UPDATED_DATE=now(), $queryFields where USER_ID=:userID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->bindValue(':adminID', SmsApiAdmin::getCurrentUser()->getID(), PDO::PARAM_INT);
			parent::bindDynamicValues($rules, $data, $stmt);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	
	public function  activateUser($userID) {
		try{
			$this->checkExistence($userID, true);
			$stmt = $this->db->prepare('update USER set ACTIVE=1 where USER_ID=?');
			$stmt->bindValue(1, $userID, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}

	public function  deactivateUser($userID) {
		try{
			$this->checkExistence($userID, true);
			$stmt = $this->db->prepare('update USER set ACTIVE=0 where USER_ID=?');
			$stmt->bindValue(1, $userID, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}

	public function  checkUserActivation($userID) {
		try{
			$this->checkExistence($userID, true);
			$stmt = $this->db->prepare('select ACTIVE=1 from USER where USER_ID=?');
			$stmt->bindValue(1, $userID, PDO::PARAM_INT);
			$stmt->execute();
			$active = $stmt->fetchColumn(0);
			return !empty($active);
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	public function  getDetailsByID($userID) {
		return $this->getDetailsByIDOrUsername($userID);
	}
	public function  getDetailsByUsername($username) {
		return $this->getDetailsByIDOrUsername(null, $username);
	}

	public function  getUsernameOfUserID($userID) {
		try {
			$query = 'select USER_NAME from USER where USER_ID=:userID';
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->execute();
			$username = $stmt->fetchColumn(0);
			unset($stmt);
			return $username;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}

	public function  getUserIDOfUsername($username) {
		try {
			$query = 'select USER_ID from USER where USER_NAME=:username';
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$stmt->execute();
			$userID = $stmt->fetchColumn(0);
			unset($stmt);
			return $userID;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	
	private function  getDetailsByIDOrUsername($userID, $username=null) {
		try {
			if(empty ($userID) && empty ($username))
				throw new InvalidArgumentException("Invalid UserID");
			$query = "select
					u.USER_ID as userID,
					u.CLIENT_ID as clientID,
					c.COMPANY_NAME as clientCompanyName,
					u.USER_NAME as userName,
					(u.ACTIVE=1) as active,
					if(u.ACTIVE,'Active','Inactive') as statusName,
					u.CREDIT as userCredit,
					u.COBRANDER_ID as cobranderID,
					(u.URL_ACTIVE=1) as statusDeliveryActive,
					if(u.URL_ACTIVE,'Active','Inactive') as statusDeliveryStatusName,
					u.DELIVERY_STATUS_URL as statusDeliveryUrl,
					u.URL_INVALID_COUNT as statusDeliveryUrlInvalidCount,
					u.URL_LAST_RETRY as statusDeliveryUrlLastRetry,
                                        u.EXPIRED_DATE as expiredDate,
					(u.USE_BLACKLIST=1) as replyBlacklistEnabled,
					if(u.USE_BLACKLIST=1,'Enabled','Disabled') as replyBlacklistStatusName,
					(u.IS_POSTPAID=1) as isPostpaid,
					if(u.IS_POSTPAID=1,'Yes','No') as isPostpaidStatusName,
					u.LAST_ACCESS as lastAccess,
					u.CREATED_DATE as createdTimestamp,
					u.CREATED_BY as createdBy,
					a1.ADMIN_DISPLAYNAME as createdByName,
					u.UPDATED_DATE as updatedTimestamp,
					u.UPDATED_BY as updatedBy,
					a2.ADMIN_DISPLAYNAME as updatedByName
					
				from USER as u
					inner join CLIENT as c on u.CLIENT_ID = c.CLIENT_ID
					inner join ADMIN as a1 on u.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on u.UPDATED_BY=a2.ADMIN_ID";

			if($username!==null){
				$stmt = $this->db->prepare("$query where u.USER_NAME=:username");
				$stmt->bindValue(':username', $username, PDO::PARAM_STR);
			}else{
				$stmt = $this->db->prepare("$query where u.USER_ID=:userID");
				$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			}
			$stmt->execute();
			$details = $stmt->fetch(PDO::FETCH_ASSOC);
			unset ($stmt);
			$details['statusDeliveryActive'] = !empty($details['statusDeliveryActive']);
			return $details;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}

	/////////////////// SENDER ID ///////////////////
	/**
	 * Get details about sender id from database
	 * @param int $senderID The sender id  (unique id)
	 * @return array The details. The array Contain the following fields:
	 *               - senderID
	 *               - senderName
	 *               - senderRangeStart
	 *               - senderRangeEnd 
	 *               - senderEnabled, whether the sender id is enabled
	 *               - senderStatusName, could either Enabled or Disabled
	 *               - userID, the user ID with which the sender record is associated
	 */
	public function getSenderDetails($senderID) {
		try {
			if(empty ($senderID))
				throw new Exception("Invalid sender ID");
			$query = "select
						SENDER_ID  as senderID,
						SENDER_NAME  as senderName,
						RANGE_START  as senderRangeStart,
						RANGE_END  as senderRangeEnd,
						SENDER_ENABLED = 1  as senderEnabled,
						IF(SENDER_ENABLED, 'Enabled', 'Disabled')  as senderStatusName,
						USER_ID as userID
					from SENDER
					where SENDER_ID=:senderID ";

			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':senderID', $senderID, PDO::PARAM_INT);
			$stmt->execute();
			$list = $stmt->fetch(PDO::FETCH_ASSOC);
			unset ($stmt);
			return $list;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	/**
	 *
	 * @param int $userID
	 * @param string $senderName
	 * @param string $rangeStart
	 * @param string $rangeEnd
	 * @param bool $enabled
	 * @return int New sender IDs
	 */
	public function addSender($userID, $senderName, $rangeStart=null, $rangeEnd=null, $enabled=false) {
		try{
			$this->checkExistence($userID, true);
			$senderName = trim($senderName);
			if($senderName==='')
				throw new InvalidArgumentException('Sender name can not be empty!');
			if(empty($rangeStart))
				$rangeStart = null;
			if(empty($rangeEnd))
				$rangeEnd = null;
			if(($rangeEnd !== null) && ($rangeStart===null))
				throw new InvalidArgumentException("Range start can not be empty if range end is given!");
			if(!$this->checkSenderNameAvailability($senderName, $userID))
				throw new Exception("Sender name '$senderName' exists for this user");
			$query = "insert into SENDER (USER_ID, SENDER_NAME, RANGE_START, RANGE_END, SENDER_ENABLED)
						values (:userID, :senderName, :rangeStart, :rangeEnd, :senderEnabled)";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->bindValue(':senderName', $senderName, PDO::PARAM_STR);
			$stmt->bindValue(':rangeStart', $rangeStart, PDO::PARAM_INT);
			$stmt->bindValue(':rangeEnd', $rangeEnd, PDO::PARAM_INT);
			$stmt->bindValue(':senderEnabled', !empty($enabled), PDO::PARAM_BOOL);
			$stmt->execute();
			$senderID = $this->db->lastInsertId();
			return $senderID;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			$this->logger->debug("Failed Query: $query\nQuery params: $userID,$senderName,$rangeStart,$rangeEnd");
			throw new Exception('Query error');
		}
	}

	public function updateSender($senderID, array $data) {
		try {
			if(empty ($senderID))
				throw new Exception("Invalid sender ID");
			if(empty ($data))
				throw new Exception("Empty data");

			$rules = array(
				'senderName'=>array('param'=>':senderName', 'field'=>'SENDER_NAME', 'type'=>PDO::PARAM_STR),
				'senderRangeStart'=>array('param'=>':senderRangeStart', 'field'=>'RANGE_START', 'type'=>PDO::PARAM_STR),
				'senderRangeEnd'=>array('param'=>':senderRangeEnd', 'field'=>'RANGE_END', 'type'=>PDO::PARAM_STR)
			);

			$senderDetails = $this->getSenderDetails($senderID);
			if(!$senderDetails){
				throw new Exception('Sender ID record was not found, ID='.$senderID);
			}
			
			if(isset($data['senderName'])){
				if($senderDetails['senderName'] != $data['senderName']){
					if(!$this->checkSenderNameAvailability($data['senderName'], $senderDetails['userID'])){
						throw new Exception("Sender name '{$data['senderName']}' exists");
					}
				}else{
					unset($data['senderName']);//do not include this
				}
			}
			
			$noSenderRangeEnd = empty($data['senderRangeEnd']);
			if($noSenderRangeEnd) {
				unset($data['senderRangeEnd']);
			}

			$queryFields = parent::buildDynamicUpdateClause($rules, $data);
			
			if($noSenderRangeEnd) {
				$queryFields .= $queryFields? ',RANGE_END=null' : 'RANGE_END=null';
			}

			if(!$queryFields)
				throw new Exception("No update field");
			$query = "update SENDER set $queryFields where SENDER_ID=:senderID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':senderID', $senderID, PDO::PARAM_INT);
			parent::bindDynamicValues($rules, $data, $stmt);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	public function  removeSender($userID, $senderID) {
		try{
			throw new RuntimeException('"removeSender" feature not yet supported');
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception('DB error');
		}
	}
	public function  enableSender($senderID) {
		try{
			if(empty($senderID))
				throw new InvalidArgumentException('Invalid sender ID!');
			$stmt = $this->db->prepare("update SENDER set SENDER_ENABLED=1 where SENDER_ID=:senderID");
			$stmt->bindValue(':senderID', $senderID, PDO::PARAM_INT);
			$stmt->execute();
			unset($stmt);
		} catch (PDOException $e) {
			$this->logger->error("$e");
			if(isset($stmt))
				unset($stmt);
			throw new Exception('Query error');
		}
	}
	public function  disableSender($senderID) {
		try{
			if(empty($senderID))
				throw new InvalidArgumentException('Invalid sender ID!');
			$stmt = $this->db->prepare("update SENDER set SENDER_ENABLED=0 where SENDER_ID=:senderID");
			$stmt->bindValue(':senderID', $senderID, PDO::PARAM_INT);
			$stmt->execute();
			unset($stmt);
		} catch (PDOException $e) {
			$this->logger->error("$e");
			if(isset($stmt))
				unset($stmt);
			throw new Exception('Query error');
		}
	}

	/////////////////// IP ///////////////////
	public function  setIPPermission($userID, $ip) {
		try{
			if(!$this->checkExistence($userID))
				throw new Exception('Attempt to update a nonexisting API user account, ID='.$userID);
			if(!filter_var($ip, FILTER_VALIDATE_IP))
				throw new InvalidArgumentException('Invalid IP: '.$ip);
			$checkStmt = $this->db->prepare("select count(*) from USER_IP where USER_ID=:userID and IP_ADDRESS=:ip");
			$checkStmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$checkStmt->bindValue(':ip', $ip, PDO::PARAM_STR);
			$checkStmt->execute();
			if($checkStmt->fetchColumn(0))
				throw new Exception('Duplicate IP: '.$ip);
			$stmt = $this->db->prepare("insert into USER_IP (USER_ID, IP_ADDRESS) values (:userID, :ip)");
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
			$stmt->execute();
			$ipID = $this->db->lastInsertId();
			unset($stmt);
			return $ipID;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			if(isset($stmt))
				unset($stmt);
			if(isset($checkStmt))
				unset($checkStmt);
			throw new Exception('DB error: '.$e->getCode());
		}
	}
	public function  unsetIPPermission($userID, $ip) {
		try{
			if(!$this->checkExistence($userID))
				throw new Exception('Attempt to update a nonexisting API user account, ID='.$userID);
			if(!filter_var($ip, FILTER_VALIDATE_IP))
				throw new InvalidArgumentException('Invalid IP: '.$ip);
			$checkStmt = $this->db->prepare("select count(*) from USER_IP where USER_ID=:userID and IP_ADDRESS=:ip");
			$checkStmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$checkStmt->bindValue(':ip', $ip, PDO::PARAM_STR);
			$checkStmt->execute();
			if(!$checkStmt->fetchColumn(0))
				throw new Exception('IP was not exist: '.$ip);
			$stmt = $this->db->prepare("delete from USER_IP where USER_ID=:userID and IP_ADDRESS=:ip");
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
			$stmt->execute();
			unset($stmt);
			return true;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			if(isset($stmt))
				unset($stmt);
			if(isset($checkStmt))
				unset($checkStmt);
			throw new Exception('DB error: '.$e->getCode());
		}
	}
	
	public function  getUserIP($userID) {
		try {
			if(empty ($userID))
				throw new Exception("Invalid UserID");
			$query = "select USER_IP_ID as ipID, IP_ADDRESS as ipAddress from USER_IP where USER_ID=:userID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->execute();
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			unset ($stmt);
			return $list? $list : array();
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}

	public function blacklistReplyNumber($userID, $msisdn) {
		try{
			if(!$this->checkExistence($userID))
				throw new Exception('Attempt to update a nonexisting API user account, ID='.$userID);

			$validMsisdn = filter_var($msisdn, FILTER_VALIDATE_REGEXP, array(
				'options'=>
					array(
						'regexp'=>  $this->userConfig['msisdnPattern']
					)
				));
			if(!$validMsisdn)
				throw new InvalidArgumentException('Invalid MSISDN: '.$msisdn);
			$checkStmt = $this->db->prepare("select count(*) from REPLY_BLACKLIST
										where USER_ID=:userID and MSISDN=:msisdn");
			$checkStmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$checkStmt->bindValue(':msisdn', $validMsisdn, PDO::PARAM_STR);
			$checkStmt->execute();
			if($checkStmt->fetchColumn(0))
				throw new Exception('Duplicate MSISDN: '.$validMsisdn);
			unset($checkStmt);
			$stmt = $this->db->prepare("insert into REPLY_BLACKLIST (USER_ID, MSISDN)
										values (:userID, :msisdn)");
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->bindValue(':msisdn', $validMsisdn, PDO::PARAM_STR);
			$stmt->execute();
			$blacklistID = $this->db->lastInsertId();
			unset($stmt);
			return $blacklistID;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			if(isset($stmt))
				unset($stmt);
			if(isset($checkStmt))
				unset($checkStmt);
			throw new Exception('DB error: '.$e->getCode());
		}
	}


	public function  unblacklistReplyNumber($userID, $msisdn) {
		try{
			if(!$this->checkExistence($userID))
				throw new Exception('Attempt to update a nonexisting API user account, ID='.$userID);
			$validMsisdn = filter_var($msisdn, FILTER_VALIDATE_REGEXP, array(
				'options'=>
					array(
						'regexp'=>  $this->userConfig['msisdnPattern']
					)
				));
			if(!$validMsisdn)
				throw new InvalidArgumentException('Invalid MSISDN: '.$msisdn);
			$checkStmt = $this->db->prepare("select count(*) from REPLY_BLACKLIST
										where USER_ID=:userID and MSISDN=:msisdn");
			$checkStmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$checkStmt->bindValue(':msisdn', $validMsisdn, PDO::PARAM_STR);
			$checkStmt->execute();
			if(!$checkStmt->fetchColumn(0))
				throw new Exception('MSISDN was not exist: '.$validMsisdn);
			$stmt = $this->db->prepare("delete from REPLY_BLACKLIST where USER_ID=:userID and MSISDN=:msisdn");
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->bindValue(':msisdn', $msisdn, PDO::PARAM_STR);
			$stmt->execute();
			unset($stmt);
			return true;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			if(isset($stmt))
				unset($stmt);
			if(isset($checkStmt))
				unset($checkStmt);
			throw new Exception('DB error: '.$e->getCode());
		}
	}

	public function  getUserReplyBlacklist($userID) {
		try {
			if(empty ($userID))
				throw new Exception("Invalid UserID");
			$query = "select REPLY_BLACKLIST_ID as replyBlacklistID, 
							MSISDN as replyBlacklistMsisdn
							from REPLY_BLACKLIST where USER_ID=:userID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->execute();
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			unset ($stmt);
			return $list? $list : array();
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}

	/////////////////// VIRTUAL NUMBER ///////////////////
	public function  getUserVirtualNumbers($userID) {
		try {
			if(empty ($userID))
				throw new Exception("Invalid UserID");
			$query = "select
						VIRTUAL_NUMBER_ID  as virtualNumberID,
						DESTINATION  as virtualDestination,
						FORWARD_URL  as virtualUrl,
						(URL_ACTIVE=1) as virtualUrlActive,
						if(URL_ACTIVE, 'Active', 'Inactive') as virtualUrlStatusName,
						URL_INVALID_COUNT  as virtualUrlInvalidCount,
						URL_LAST_RETRY  as virtualUrlLastRetry
					from VIRTUAL_NUMBER
					where USER_ID=:userID ";

			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->execute();
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			unset ($stmt);
			return $list? $list : array();
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	
	public function  getVirtualNumberDetails($virtualNumberID) {
		try {
			if(empty ($virtualNumberID))
				throw new Exception("Invalid virtualNumberID");
			$query = "select
						VIRTUAL_NUMBER_ID  as virtualNumberID,
						DESTINATION  as virtualDestination,
						FORWARD_URL  as virtualUrl,
						(URL_ACTIVE=1) as virtualUrlActive,
						if(URL_ACTIVE, 'Active', 'Inactive') as virtualUrlStatusName,
						URL_INVALID_COUNT  as virtualUrlInvalidCount,
						URL_LAST_RETRY  as virtualUrlLastRetry
					from VIRTUAL_NUMBER
					where VIRTUAL_NUMBER_ID=:virtualNumberID ";

			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':virtualNumberID', $virtualNumberID, PDO::PARAM_INT);
			$stmt->execute();
			$details = $stmt->fetch(PDO::FETCH_ASSOC);
			unset ($stmt);
			return $details;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	public function  addVirtualNumber($userID, $data) {
		try {
			$this->checkExistence($userID, true);
			$destination = isset($data['virtualDestination'])? trim($data['virtualDestination']) : '';
			$url = isset($data['virtualUrl'])? trim($data['virtualUrl']) : '';
			$active = $url != '';
			if($destination=='')
				throw new InvalidArgumentException('Empty destination');
			$query = "insert into VIRTUAL_NUMBER
						(USER_ID, DESTINATION, FORWARD_URL, URL_ACTIVE, URL_INVALID_COUNT, URL_LAST_RETRY)
						values
						(:userID, :virtualDestination, :virtualUrl, :virtualUrlActive, null, null)";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->bindValue(':virtualDestination', $destination, PDO::PARAM_STR);
			$stmt->bindValue(':virtualUrl', $url, PDO::PARAM_STR);
			$stmt->bindValue(':virtualUrlActive', $active? 1 : 0, PDO::PARAM_INT);
			$stmt->execute();
			$id = $this->db->lastInsertId();
			unset ($stmt);
			return $id;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	public function  updateVirtualNumber($virtualNumberID, $data) {
		try {
			if(empty($virtualNumberID))
				throw new Exception('Invalid virtual number ID');
			$destination = isset($data['virtualDestination'])? trim($data['virtualDestination']) : '';
			$url = isset($data['virtualUrl'])? trim($data['virtualUrl']) : '';
//			$active = !empty($data['virtualUrlActive']);
			$active = $url != '';
			if($destination=='')
				throw new InvalidArgumentException('Empty destination');
//			if($active && ($url==''))
//				throw new InvalidArgumentException('Empty forward URL');

			$query = "update VIRTUAL_NUMBER
						set DESTINATION=:virtualDestination,
							FORWARD_URL=:virtualUrl,
							URL_ACTIVE=:virtualUrlActive,
							URL_INVALID_COUNT=0,
							URL_LAST_RETRY=null
						where VIRTUAL_NUMBER_ID=:virtualNumberID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':virtualNumberID', $virtualNumberID, PDO::PARAM_INT);
			$stmt->bindValue(':virtualDestination', $destination, PDO::PARAM_STR);
			$stmt->bindValue(':virtualUrl', $url, PDO::PARAM_STR);
			$stmt->bindValue(':virtualUrlActive', $active? 1 : 0, PDO::PARAM_INT);
			$stmt->execute();
			unset ($stmt);
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	public function  removeVirtualNumber($virtualNumberID) {
		try {
			if(empty($virtualNumberID))
				throw new Exception('Invalid virtual number ID');
			$query = "delete from VIRTUAL_NUMBER where VIRTUAL_NUMBER_ID=:virtualNumberID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':virtualNumberID', $virtualNumberID, PDO::PARAM_INT);
			$stmt->execute();
			unset ($stmt);
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query failed");
		}
	}
	
	/////////////////// CHECK ///////////////////
	/**
	 * Check if an user record exists in data base
	 * @param int $userID The user id
	 * @param bool $autoException Throw exception when failed
	 * @return bool success or not
	 */
	public function  checkExistence($userID, $autoException=false) {
		try {
			if(empty($userID)){
				if($autoException)
					throw new Exception("Invalid user ID ($userID)");
				return false;
			}
			$query = 'select count(*) from USER where USER_ID=:userID';
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->fetchColumn(0) <= 0){
				if($autoException)
					throw new Exception('Can not find specified API user!');
				return false;
			}
			return true;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}

	public function  checkUsernameAvailability($userName) {
		try {
			if(!$this->checkUserNameFormat($userName))
				throw new InvalidArgumentException('Invalid user name format');
			$query = 'select count(*) from USER where USER_NAME=:userName';
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
			$stmt->execute();
			$available = $stmt->fetchColumn(0) == 0;
			unset($stmt);
			return $available;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Error while checking API user existence");
		}
	}

	/**
	 * Check if sender name is differ from stored
	 * @param string $senderName Requested sender name
	 * @param int $senderID User ID
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	protected function  checkIsSenderNameModified($senderName, $senderID) {
		try {
			if(empty($senderID))
				throw new InvalidArgumentException('Invalid sender ID');
			$query = 'select SENDER_NAME from SENDER where SENDER_ID=:senderID';
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':senderID', $senderID, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() < 1)
				throw new Exception('Sender record was not found');
			$storedSenderName = $stmt->fetchColumn(0);
			$isModified = "$storedSenderName" !== "$senderName";
			return $isModified;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error while checking sender ID modification");
		}
	}
	/**
	 * Check if requested user name is differ than that is stored
	 * @param string $userName Requested user name
	 * @param int $userID User ID
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	protected function  checkIsUserNameModified($userName, $userID) {
		try {
			if("$userID"==="")
				throw new InvalidArgumentException('Invalid user name');
			$query = 'select USER_NAME from USER where USER_ID=:userID';
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() < 1)
				throw new Exception('User record was not found');
			$storedUserName = $stmt->fetchColumn(0);
			$isModified = "$storedUserName" !== "$userName";
			return $isModified;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error while checking user name modification");
		}
	}
	/**
	 * Check is sender name is available
	 * @param string $senderName The sender name
	 * @param int $userID The API user ID of which the sender will be registered for
	 *                       This param is required to enable prevention of a sender id
	 *                       registered twice for a user, while still allowing
	 *                       many-to-many relations for sender id and apiuser
	 * @return bool
	 */
	public function  checkSenderNameAvailability($senderName, $userID) {
		try {
			if(!$this->checkSenderNameFormat($senderName))
				throw new InvalidArgumentException('Invalid sender name format');
			
			$query = 'select SENDER_NAME from SENDER where SENDER_NAME=:senderName and USER_ID=:userId';
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':senderName', $senderName, PDO::PARAM_STR);
			$stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
			$stmt->execute();

			if(!$stmt->rowCount()){ // no records
				unset($stmt); //free result
				return true;
			}
			//aware about MySQL *_ci collations
			$senderName = "$senderName";//force string type
			$stmt->setFetchMode(PDO::FETCH_NUM);
			while($data = $stmt->fetch()){
				if($data[0] == $senderName){ //php string comparioson is case sensitive
					unset($stmt); //free result
					return false;
				}
			}
			unset($stmt);
			return true;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Error while checking sender name availbility");
		}
	}
	public function  checkSenderNameFormat($senderName) {
		try {
			if('' === (string) $senderName)
				return false;
//			return true;
			$check = filter_var($senderName, FILTER_VALIDATE_REGEXP, array(
				'options'=>
					array(
						'regexp'=>  $this->userConfig['senderNamePattern']
					)
				));
			return $check !== false;
		} catch (Exception $e) {
			$this->logger->error("$e");
			return false;
		}
	}
	public function  checkUserNameFormat($userName) {
		try {
			if('' === (string) $userName)
				return false;
			return true;
			$check = filter_var($userName, FILTER_VALIDATE_REGEXP, array(
				'options'=>
					array(
						'regexp'=>  $this->userConfig['userNamePattern']
					)
				));
			return $check !== false;
		} catch (Exception $e) {
			$this->logger->error("$e");
			return false;
		}
	}
}
