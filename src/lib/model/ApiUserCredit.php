<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once SMSAPIADMIN_LIB_DIR.'model/ApiUser.php';
/**
 * Description of ApiUserCredit
 *
 * @author setia.budi
 */
class ApiUserCredit extends ApiBaseModel{
	
	const TYPE_TOPUP = 'T';
	const TYPE_DEDUCT = 'D';

	/**
	 *
	 * @var ApiUser
	 */
	private $apiuser;

	public function __construct() {
		parent::__construct();
		$this->apiuser = new ApiUser();
		SmsApiAdmin::loadConfig('transaction');
	}

	public function getTransactionHistory($userID){
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            if (!$this->apiuser->checkExistence($userID))
				throw new Exception('User not found!');
			$query = "select
					c.CREDIT_TRANSACTION_ID as creditTransactionID,
					c.TRANSACTION_REF as transactionRef,
					c.USER_ID as userID,
					u.USER_NAME as userName,
					c.CREDIT_REQUESTER as transactionRequester,
					c.CREDIT_AMOUNT as transactionCredit,
					c.CREDIT_PRICE as transactionPrice,
					c.CURRENCY_CODE as transactionCurrency,
					c.PAYMENT_METHOD as paymentMethod,
					c.PAYMENT_DATE as paymentDate,
					(c.PAYMENT_ACK=1) as paymentAcknowledged,
					if(c.PAYMENT_ACK=1,'Paid','Not Paid') as paymentStatusName,
					c.TRANSACTION_REMARK as transactionRemark,
					c.CREATED_BY as transactionCreatedBy,
					a1.ADMIN_DISPLAYNAME as transactionCreatedByName,
					c.CREATED_DATE as transactionCreatedDate,
					c.UPDATED_BY as transactionUpdatedBy,
					a2.ADMIN_DISPLAYNAME as transactionUpdatedByName,
					c.UPDATED_DATE as transactionUpdatedDate
				from CREDIT_TRANSACTION as c
					inner join USER as u on c.USER_ID=u.USER_ID
					inner join ADMIN as a1 on c.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on c.UPDATED_BY=a2.ADMIN_ID
				where c.USER_ID=:userID
				order by c.CREATED_DATE desc";
            $stmt = $db->prepare($query);
			$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
			$stmt->execute();
            $getHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $getHistory;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception('Query error');
		}
	}
	public function getTransactionDetailsByID($tranID){
		return $this->getTransactionDetailsByIDOrRef($tranID);
	}
	public function getTransactionDetailsByRef($tranRef){
		return $this->getTransactionDetailsByIDOrRef(null, $tranRef);
	}
	public function getTransactionDetailsByIDOrRef($tranID, $tranRef=null){
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
			$query = "select
					c.CREDIT_TRANSACTION_ID as creditTransactionID,
					c.TRANSACTION_REF as transactionRef,
					c.USER_ID as userID,
					u.USER_NAME as userName,
					c.CREDIT_REQUESTER as transactionRequester,
					c.CREDIT_AMOUNT as transactionCredit,
					c.CREDIT_PRICE as transactionPrice,
					c.CURRENCY_CODE as transactionCurrency,
					c.PAYMENT_METHOD as paymentMethod,
					c.PAYMENT_DATE as paymentDate,
					(c.PAYMENT_ACK=1) as paymentAcknowledged,
					if(c.PAYMENT_ACK=1,'Paid','Not Paid') as paymentStatusName,
					c.TRANSACTION_REMARK as transactionRemark,
					c.CREATED_BY as transactionCreatedBy,
					a1.ADMIN_DISPLAYNAME as transactionCreatedByName,
					c.CREATED_DATE as transactionCreatedDate,
					c.UPDATED_BY as transactionUpdatedBy,
					a2.ADMIN_DISPLAYNAME as transactionUpdatedByName,
					c.UPDATED_DATE as transactionUpdatedDate
				from CREDIT_TRANSACTION as c
					inner join USER as u on c.USER_ID=u.USER_ID
					inner join ADMIN as a1 on c.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on c.UPDATED_BY=a2.ADMIN_ID
				where ";
			if($tranRef === null){
				$query.=' c.CREDIT_TRANSACTION_ID=?';
				$key = filter_var($tranID, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
				if(!$key)
					throw new InvalidArgumentException("Invalid transaction ID: $tranID");
				$type = PDO::PARAM_INT;
			}elseif($tranID === null){
				$query.=' c.TRANSACTION_REF=?';
				$key = filter_var($tranRef, FILTER_SANITIZE_STRING);
				if(!$key)
					throw new InvalidArgumentException("Invalid transaction reference: $tranRef");
				$type = PDO::PARAM_STR;
			}else{
				throw new InvalidArgumentException("Missing transaction ID/Ref : $tranID/$tranRef");
			}			

            $stmt = $db->prepare($query);
			$stmt->bindValue(1, $key, $type);
			$stmt->execute();
			if(!$stmt->rowCount())
				return array();
            $getDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            return $getDetails;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception('Query error');
		}
	}

	public function getUserCredit($userID){
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
			$this->apiuser->checkExistence($userID, true);
			$query = "select CREDIT from USER where USER_ID=?";
            $stmt = $db->prepare($query);
			$stmt->bindValue(1, $userID, PDO::PARAM_INT);
			$stmt->execute();
			if(!$stmt->rowCount())
				return 0;
            $getUserCredit = $stmt->fetchColumn(0);
            return $getUserCredit;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception('Query error');
		}
	}
	/**
	 * Top up user credit
	 * @param <type> $userID
	 * @param array $transaction
	 * @return <type>
	 */
    public function topUp($userID, array $transaction) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
			$this->apiuser->checkExistence($userID, true);
			$rules = SmsApiAdmin::getConfig('transaction');
			
			$logQuery = "insert into CREDIT_TRANSACTION (
							TRANSACTION_REF,USER_ID,
							CREDIT_AMOUNT,CREDIT_PRICE,CURRENCY_CODE,
							CREDIT_REQUESTER, PAYMENT_METHOD,PAYMENT_DATE,PAYMENT_ACK,
							CREATED_BY,CREATED_DATE,UPDATED_BY,UPDATED_DATE,
							TRANSACTION_REMARK
						)
						values (
							:transactionRef, :userID,
							:transactionCredit, :transactionPrice, :transactionCurrency,
							:transactionRequester,:paymentMethod, NULL, 0,
							:adminID, now(), NULL, NULL ,
							:transactionRemark
						)";
			$creditQuery = 'update USER set CREDIT=CREDIT+:transactionCredit where USER_ID=:userID';
			
			if(!isset($transaction['transactionCredit']))
				throw new InvalidArgumentException('Missing credit amount in arguments');
			if(!isset($transaction['transactionRequester']))
				throw new InvalidArgumentException('Missing transaction requester in arguments');
			if(!isset($transaction['transactionPrice']))
				throw new InvalidArgumentException('Missing price in arguments');
			if(!isset($transaction['transactionCurrency']))
				throw new InvalidArgumentException('Missing currency in arguments');
			if(!isset($transaction['paymentMethod']))
				throw new InvalidArgumentException('Missing payment method amount in arguments');
			if(!isset($transaction['transactionRemark']))
				$transaction['transactionRemark']='';

			$transactionCredit = filter_var($transaction['transactionCredit'],
										FILTER_SANITIZE_NUMBER_INT,
										array('options'=>array('min_range'=>1)));
			$transactionPrice = filter_var($transaction['transactionPrice'],
										FILTER_SANITIZE_NUMBER_FLOAT,
										array(
											'options'=>array('min_range'=>0),
											'flags'=>FILTER_FLAG_ALLOW_FRACTION)
										);
			$transactionCurrency = trim(filter_var($transaction['transactionCurrency'],
										FILTER_SANITIZE_STRING,
										array('flags'=>FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
			$paymentMethod = trim(filter_var($transaction['paymentMethod'],
										FILTER_SANITIZE_STRING,
										array('flags'=>FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
			$transactionRequester = trim(filter_var($transaction['transactionRequester'],
										FILTER_SANITIZE_STRING));
			$transactionRemark = filter_var($transaction['transactionRemark'],
										FILTER_SANITIZE_STRING);
			$transactionRef = self::generateTransactionRefCode(self::TYPE_TOPUP);
			$adminID = SmsApiAdmin::getCurrentUser()->getID();

			if($transactionCredit === false)
				throw new Exception("Invalid credit amount: $transactionCredit");
			if($transactionPrice === false)
				throw new Exception("Invalid price: $transactionPrice");
			if($transactionCurrency == '')
				throw new Exception('Invalid currency!');
			if($transactionRequester == '')
				throw new Exception('Invalid transaction requester name!');
			if($paymentMethod == '')
				throw new Exception('Invalid payment method!');

            $logStmt = $db->prepare($logQuery);
			$logStmt->bindValue(':transactionCredit', $transactionCredit, PDO::PARAM_INT);
			$logStmt->bindValue(':transactionPrice', $transactionPrice, PDO::PARAM_STR);
			$logStmt->bindValue(':transactionCurrency', $transactionCurrency, PDO::PARAM_STR);
			$logStmt->bindValue(':paymentMethod', $paymentMethod, PDO::PARAM_STR);
			$logStmt->bindValue(':transactionRequester', $transactionRequester, PDO::PARAM_STR);
			$logStmt->bindValue(':transactionRemark', $transactionRemark, PDO::PARAM_STR);
			$logStmt->bindValue(':transactionRef', $transactionRef, PDO::PARAM_STR);
			$logStmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
			$logStmt->bindValue(':userID', $userID, PDO::PARAM_INT);

            $creditStmt = $db->prepare($creditQuery);
			$creditStmt->bindValue(':transactionCredit', $transactionCredit, PDO::PARAM_INT);
			$creditStmt->bindValue(':userID', $userID, PDO::PARAM_INT);

            $db->beginTransaction();
            try {
				$logStmt->execute();
                $transactionID = $db->lastInsertId();
				$creditStmt->execute();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
				$this->logger->error("$e");
				throw new Exception("Transaction has failed, query was rolled back");
			}
			return $transactionID;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}

    public function deduct($userID, array $transaction) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
			$this->apiuser->checkExistence($userID, true);
			$rules = SmsApiAdmin::getConfig('transaction');

			$logQuery = "insert into CREDIT_TRANSACTION (
							TRANSACTION_REF,USER_ID,
							CREDIT_AMOUNT,CREDIT_PRICE,CURRENCY_CODE,
							CREDIT_REQUESTER, PAYMENT_METHOD,PAYMENT_DATE,PAYMENT_ACK,
							CREATED_BY,CREATED_DATE,UPDATED_BY,UPDATED_DATE,
							TRANSACTION_REMARK
						)
						values (
							:transactionRef, :userID,
							:transactionCredit, 0.0, :transactionCurrency,
							'-','', curdate(),1,
							:adminID, now(), NULL, NULL ,
							:transactionRemark
						)";
			$creditQuery = 'update USER set CREDIT=CREDIT-:transactionCredit where USER_ID=:userID';

			if(!isset($transaction['transactionCredit']))
				throw new InvalidArgumentException('Missing credit amount in arguments');

			$transactionCredit = filter_var($transaction['transactionCredit'],
										FILTER_SANITIZE_NUMBER_INT,
										array('options'=>array('min_range'=>1)));
			$transactionRemark = trim(filter_var($transaction['transactionRemark'],
										FILTER_SANITIZE_STRING,
										array('flags'=>FILTER_FLAG_STRIP_LOW)));
			$transactionRef = self::generateTransactionRefCode(self::TYPE_DEDUCT);
			$adminID = SmsApiAdmin::getCurrentUser()->getID();

			if($transactionCredit === false)
				throw new Exception("Invalid credit amount: $transactionCredit");

			$currentBalance = $this->getUserCredit($userID);
			if($currentBalance < $transactionCredit)
				throw new Exception("Can not deduct user credit more than current balance. Requested=$transactionCredit, balance=$currentBalance");

            $logStmt = $db->prepare($logQuery);
			$logStmt->bindValue(':transactionCredit', -$transactionCredit, PDO::PARAM_INT);
			$logStmt->bindValue(':transactionRemark', $transactionRemark, PDO::PARAM_STR);
			$logStmt->bindValue(':transactionCurrency', $rules['defaultCurrency'], PDO::PARAM_STR);
			$logStmt->bindValue(':transactionRef', $transactionRef, PDO::PARAM_STR);
			$logStmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
			$logStmt->bindValue(':userID', $userID, PDO::PARAM_INT);

            $creditStmt = $db->prepare($creditQuery);
			$creditStmt->bindValue(':transactionCredit', $transactionCredit, PDO::PARAM_INT);
			$creditStmt->bindValue(':userID', $userID, PDO::PARAM_INT);

            $db->beginTransaction();
            try {
				$logStmt->execute();
                $transactionID = $db->lastInsertId();
				$creditStmt->execute();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
				$this->logger->error("$e");
				throw new Exception("Transaction has failed, query was rolled back");
			}
			return $transactionID;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}

    public function acknowledgePayment($tranID, array $payment) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
			$this->checkExistence($tranID, true);
			if(empty($payment))
				throw new InvalidArgumentException('No payment details');
			$rules = array(
				'paymentDate'=>array('param'=>':paymentDate', 'field'=>'PAYMENT_DATE', 'type'=>PDO::PARAM_STR),
				'transactionRemark'=>array('param'=>':transactionRemark', 'field'=>'TRANSACTION_REMARK', 'type'=>PDO::PARAM_STR)
			);
			$queryFields = parent::buildDynamicUpdateClause($rules, $payment);
			if(!$queryFields)
				throw new Exception("No update field");
			if(isset($payment['paymentDate'])){
				$payment['paymentDate'] = trim($payment['paymentDate']);
				if(!$payment['paymentDate'])
					throw new InvalidArgumentException('Sender name can not be empty');
				if(strptime($payment['paymentDate'], '%Y-%m-%d %H:%M:%S'))
					throw new InvalidArgumentException('Invalid date format');
			}

			$query = "update CREDIT_TRANSACTION set
						PAYMENT_ACK=1,
						UPDATED_BY=:adminID,
						UPDATED_DATE=now(),
						$queryFields where CREDIT_TRANSACTION_ID=:tranID";
            $stmt = $db->prepare($query);
			$stmt->bindValue(':tranID', $tranID, PDO::PARAM_INT);
			$stmt->bindValue(':adminID', SmsApiAdmin::getCurrentUser()->getID(), PDO::PARAM_INT);
			parent::bindDynamicValues($rules, $payment, $stmt);
			$stmt->execute();
            $rowCount = $stmt->rowCount() > 0;
            return $rowCount;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception('Query error!');
		}
	}

    public function updateTransaction($tranID, array $updates) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            if (!$this->checkIsTransactionEditable($tranID, true))
				throw Exception('Closed transaction is not editable!');
			if(empty ($updates))
				throw new Exception("Empty data");
			$rules = array(
				'transactionCurrency'=>array('param'=>':transactionCurrency', 'field'=>'CURRENCY_CODE', 'type'=>PDO::PARAM_STR),
				'transactionPrice'=>array('param'=>':transactionPrice', 'field'=>'CREDIT_PRICE', 'type'=>PDO::PARAM_INT),
				'transactionRequester'=>array('param'=>':transactionRequester', 'field'=>'CREDIT_REQUESTER', 'type'=>PDO::PARAM_STR),
				'transactionRemark'=>array('param'=>':transactionRemark', 'field'=>'TRANSACTION_REMARK', 'type'=>PDO::PARAM_STR)
			);
			$queryFields = parent::buildDynamicUpdateClause($rules, $updates);
			if(!$queryFields)
				throw new Exception("No update field");
			$query = "update CREDIT_TRANSACTION set UPDATED_BY=:adminID, UPDATED_DATE=now(), $queryFields where CREDIT_TRANSACTION_ID=:tranID";
            $stmt = $db->prepare($query);
			$stmt->bindValue(':tranID', $tranID, PDO::PARAM_INT);
			$stmt->bindValue(':adminID', SmsApiAdmin::getCurrentUser()->getID(), PDO::PARAM_INT);
			parent::bindDynamicValues($rules, $updates, $stmt);
			$stmt->execute();
            $rowCount = $stmt->rowCount() > 0;
            return $rowCount;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception('Query error!');
		}
	}

	public function  getRefOfTransactionID($tranID) {
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            if (empty($tranID))
				throw new Exception("Invalid transaction ID ($tranID)");
			$query = 'select TRANSACTION_REF from CREDIT_TRANSACTION where CREDIT_TRANSACTION_ID=?';
            $stmt = $db->prepare($query);
			$stmt->bindValue(1, $tranID, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()<=0)
				return null;
            $getRef = $stmt->fetchColumn(0);
            return $getRef;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}
	public function  getIDOfTransactionRef($tranRef) {
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            if (empty($tranRef))
				throw new Exception("Invalid transaction ID ($tranRef)");
			$query = 'select CREDIT_TRANSACTION_ID from CREDIT_TRANSACTION where TRANSACTION_REF=?';
            $stmt = $db->prepare($query);
			$stmt->bindValue(1, $tranRef, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount()<=0)
				return null;
            $getId = $stmt->fetchColumn(0);
            return $getId;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}
	public function  checkExistence($tranID, $autoException=false) {
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            if (empty($tranID)) {
                if ($autoException)
					throw new Exception("Invalid transaction ID ($tranID)");
				return false;
			}
			$query = 'select count(*) from CREDIT_TRANSACTION where CREDIT_TRANSACTION_ID=?';
            $stmt = $db->prepare($query);
			$stmt->bindValue(1, $tranID, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->fetchColumn(0) <= 0){
				if($autoException)
					throw new Exception("Can not find specified transaction record (ID=$tranID)");
				return false;
			}
			return true;
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}

	public function checkIsTransactionAcknowledgeable($tranID){
//		try {
			return $this->checkIsTransactionEditable($tranID);
//		} catch (PDOException $e) {
//			$this->logger->error("$e");
//			throw new Exception("Query error");
//		}
	}

	public function checkIsTransactionEditable($tranID){
		try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            if (empty($tranID))
				throw new Exception("Invalid transaction ID ($tranID)");
			$query = 'select PAYMENT_ACK=1 from CREDIT_TRANSACTION where CREDIT_TRANSACTION_ID=?';
            $stmt = $db->prepare($query);
			$stmt->bindValue(1, $tranID, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount()<1)
				throw new Exception("Rransaction does not exist (ID=$tranID)");
			$acknowledged = $stmt->fetchColumn(0);
			return empty($acknowledged);
		} catch (PDOException $e) {
			$this->logger->error("$e");
			throw new Exception("Query error");
		}
	}
	
	private static function generateTransactionRefCode($type){
		if(($type !== self::TYPE_TOPUP) && ($type !== self::TYPE_DEDUCT))
			throw new Exception('Invalid transaction type: '.$type);
		$ref = $type.gmdate('Ymd');
		$i=0;
		while(++$i<6)
			$ref.= chr(65+mt_rand(0, 25));
		return $ref;
	}


}
?>
