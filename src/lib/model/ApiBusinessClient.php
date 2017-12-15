<?php

/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once '../lib/FirePHPCore/FirePHP.class.php';

/**
 * Description of SmsApiAccount
 *
 * @author setia.budi
 * 
 * @author Fathir Wafda --> add insertBilling, updateBilling and getBillingDetail
 */
class ApiBusinessClient extends ApiBaseModel {

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * @return array
     */
    public function getAll() {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'select
					c.CLIENT_ID as clientID,
					c.COMPANY_NAME as companyName,
					c.COMPANY_URL as companyUrl,
					c.COUNTRY_CODE as countryCode,
					cn.COUNTRY_NAME as countryName,
					c.CONTACT_NAME as contactName,
					c.CONTACT_EMAIL as contactEmail,
					c.CONTACT_PHONE as contactPhone,
					c.CREATED_BY as createdBy,
					a1.ADMIN_DISPLAYNAME as createdByName,
					c.CREATED_DATE as createdTimestamp,
					c.UPDATED_BY as updatedBy,
					a2.ADMIN_DISPLAYNAME as updatedByName,
					c.UPDATED_DATE as updatedTimestamp
				from CLIENT as c
					inner join ADMIN as a1 on c.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on c.UPDATED_BY=a2.ADMIN_ID
					left join COUNTRY as cn on c.COUNTRY_CODE=cn.COUNTRY_CODE
				order by c.COMPANY_NAME';
            $getAll = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            return $getAll;
        } catch (PDOException $e) {
            $this->logger->error("$e");
            throw new Exception("Query error");
            throw $e;
        }
    }

    public function getAllPaired() {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'select CLIENT_ID, COMPANY_NAME from CLIENT order by COMPANY_NAME asc';
            $result = $db->query($query);
            $result->setFetchMode(PDO::FETCH_NUM);
            $list = array();
            while ($fields = $result->fetch()) {
                $list[$fields[0]] = $fields[1];
            }
            return $list;
        } catch (PDOException $e) {
            $this->logger->error("$e");
            throw new Exception("Query error");
            throw $e;
        }
    }

    public function register($data) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'insert into CLIENT (
						COMPANY_NAME, COMPANY_URL, COUNTRY_CODE,
						CONTACT_NAME, CONTACT_EMAIL, CONTACT_PHONE,
						CREATED_BY, CREATED_DATE
						)
					values (
						:companyName, :companyUrl, :countryCode,
						:contactName, :contactEmail, :contactPhone,
						:adminID, now()
						)';
            $adminID = SmsApiAdmin::getCurrentUser()->getID();
            $stmt = $db->prepare($query);
            $stmt->bindValue(':companyName', $data['companyName'], PDO::PARAM_STR);
            $stmt->bindValue(':companyUrl', $data['companyUrl'], PDO::PARAM_STR);
            $stmt->bindValue(':countryCode', $data['countryCode'], PDO::PARAM_STR);
            $stmt->bindValue(':contactName', $data['contactName'], PDO::PARAM_STR);
            $stmt->bindValue(':contactEmail', $data['contactEmail'], PDO::PARAM_STR);
            $stmt->bindValue(':contactPhone', $data['contactPhone'], PDO::PARAM_STR);
            $stmt->bindValue(':adminID', $adminID, PDO::PARAM_INT);
            $stmt->execute();
            $lastInsertId = $db->lastInsertId();
            return $lastInsertId;
        } catch (PDOException $e) {
            $this->logger->error("$e");
            throw new Exception("Query error!");
        }
    }

    /**
     * add sparation ';'
     * @param type $datas
     * @return type
     */
    private function addSparation($datas) {
        $i = 0;
        $string = '';
        foreach ($datas as $data) {
            if ($i != 0) {
                $string .= ';';
            }
            $string .= $data;
            $i++;
        }
        if ($string == '') {
            $string = null;
        }
        return $string;
    }

    /**
     * Insert Billing Data 
     * @param type $clientData, $data
     * @return type
     */
    public function insertBilling($clientData, $data) {
        try {

                    $firephp = FirePHP::getInstance(true);
                    $firephp->log($data);
            //$firephp->fb($clientData);
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'insert into BILLING_OPTIONS (
						CLIENT_ID, BILLED_SMS, BILLED_SMS_DESC,
						UNKNOWN, PENDING, UNDELIVERED,
						DELIVERED, DELIVERED_DESC, TOTAL_SMS,
                                                TOTAL_CHARGE, PROVIDER, PROVIDER_DESC
						)
					values (
						:clientID, :billsms, :subIdBillNo,
						:unknown, :pending, :undelivered,
						:showDelivered, :deliveredDesc, :totalSms,
                                                :totalCharge, :showProvider, :provided
						)';
            $companyID = SmsApiAdmin::getCurrentUser()->getID();
            $i = 0;
            $provided = '';
$firephp->fb($query);
            $provided = $this->addSparation($data['provided']);
            $billsms = $this->addSparation($data['billsms']);

            if ($data['errorCode'] !== null) {
                $billsms .= ';' . $data['errorCode'];
            }
            //$firephp->log($billsms);
            $stmt = $db->prepare($query);
            $stmt->bindValue(':clientID', $data['clientID'], PDO::PARAM_STR);
            $stmt->bindValue(':billsms', $billsms, PDO::PARAM_STR);
            $stmt->bindValue(':subIdBillNo', $data['subIdBillNo'], PDO::PARAM_STR);
            $stmt->bindValue(':unknown', $data['unknown'], PDO::PARAM_STR);
            $stmt->bindValue(':pending', $data['pending'], PDO::PARAM_STR);
            $stmt->bindValue(':undelivered', $data['undelivered'], PDO::PARAM_STR);
            $stmt->bindValue(':showDelivered', $data['showDelivered'], PDO::PARAM_STR);
            $stmt->bindValue(':deliveredDesc', $data['deliveredDesc'], PDO::PARAM_STR);
            $stmt->bindValue(':totalSms', $data['totalSms'], PDO::PARAM_STR);
            $stmt->bindValue(':totalCharge', $data['totalCharge'], PDO::PARAM_STR);
            $stmt->bindValue(':showProvider', $data['showProvider'], PDO::PARAM_STR);
            $stmt->bindValue(':provided', $provided, PDO::PARAM_STR);

            $stmt->execute();

            $lastInsertId = $db->lastInsertId();
$firephp->log($lastInsertId);
            return $lastInsertId;
        } catch (PDOException $e) {
            $this->logger->error("$e");
            throw new Exception("Query error!");
        }
    }

    public function updateBilling($clientID, $updates) {
        try {
            $firephp = FirePHP::getInstance(true);
//            $firephp->log($data);
//            $firephp->fb($clientData);
            
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);

            $provided = $this->addSparation($updates['provided']);
            $billsms = $this->addSparation($updates['billsms']);

            if ($updates['errorCode'] !== null) {
                $billsms .= ';' . $updates['errorCode'];
            }
            $firephp->log($billsms);
            
            $fields = array(
                'billsms' => array(':billsms', 'BILLED_SMS', $billsms, PDO::PARAM_STR),
                'subIdBillNo' => array(':subIdBillNo', 'BILLED_SMS_DESC', PDO::PARAM_STR),
                'unknown' => array(':unknown', 'UNKNOWN', PDO::PARAM_STR),
                'pending' => array(':pending', 'PENDING', PDO::PARAM_STR),
                'undelivered' => array(':undelivered', 'UNDELIVERED', PDO::PARAM_STR),
                'showDelivered' => array(':showDelivered', 'DELIVERED', PDO::PARAM_STR),
                'deliveredDesc' => array(':deliveredDesc', 'DELIVERED_DESC', PDO::PARAM_STR),
                'totalSms' => array(':totalSms', 'TOTAL_SMS', PDO::PARAM_STR),
                'showProvider' => array(':showProvider', 'PROVIDER', PDO::PARAM_STR),
                'provided' => array(':provided', 'PROVIDER_DESC', $provided, PDO::PARAM_STR)
            );
   
            $query = 'update BILLING_OPTIONS set ';
            $assignment = array();
            
            foreach ($updates as $key => $value) {
                if (isset($fields[$key])) {
                    $definition = $fields[$key];
                    $assignment[$key] = $definition[1] . '=' . $definition[0];
                }
            }
            
            foreach (array_diff_key($fields,$assignment) as $key => $value){
                if (isset($fields[$key])) {
                    $definition = $fields[$key];
                    $assignment[$key] = $definition[1] . '=' . $definition[0];
                }
            }
            
            
            if (!$assignment)
                throw new Exception("No valid update field");
            $stmt = $db->prepare($query . implode(',', $assignment) . ' where CLIENT_ID=:clientID');
            foreach ($assignment as $key => $value) {
                $definition = $fields[$key];
                switch ($key) {
                    case 'billsms':
                        $stmt->bindValue(':billsms', $billsms, PDO::PARAM_STR);
                        break;
                    case 'provided':
                        $stmt->bindValue(':provided', $provided, PDO::PARAM_STR);
                        break;
                    default :
                        $stmt->bindValue($definition[0], $updates[$key], PDO::PARAM_STR);
                }
            }
            
            $stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
            $db->beginTransaction();
            try {
                $stmt->execute();
                $db->commit();
            } catch (PDOException $e) {
                $this->logger->error("Insert client query failed. $e");
                $db->rollBack();
                throw new Exception("Failed registering new client");
            }
            return true;
        } catch (Exception $e) {
            $this->logger->error("$e");
            return false;
        }
    }

    public function update($clientID, $updates) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $fields = array(
                'companyName' => array(':companyName', 'COMPANY_NAME', PDO::PARAM_STR),
                'companyUrl' => array(':companyUrl', 'COMPANY_URL', PDO::PARAM_STR),
                'countryCode' => array(':countryCode', 'COUNTRY_CODE', PDO::PARAM_STR),
                'contactName' => array(':contactName', 'CONTACT_NAME', PDO::PARAM_STR),
                'contactEmail' => array(':contactEmail', 'CONTACT_EMAIL', PDO::PARAM_STR),
                'contactPhone' => array(':contactPhone', 'CONTACT_PHONE', PDO::PARAM_STR)
            );
            $query = 'update CLIENT set UPDATED_BY=:adminID, UPDATED_DATE=now(),';
            $assignment = array();
            foreach ($updates as $key => $value) {
                if (isset($fields[$key])) {
                    $definition = $fields[$key];
                    $assignment[$key] = $definition[1] . '=' . $definition[0];
                }
            }
            if (!$assignment)
                throw new Exception("No valid update field");
            $stmt = $db->prepare($query . implode(',', $assignment) . ' where CLIENT_ID=:clientID');
            foreach ($assignment as $key => $value) {
                $definition = $fields[$key];
                $stmt->bindValue($definition[0], $updates[$key], $definition[2]);
            }
            $stmt->bindValue(':adminID', SmsApiAdmin::getCurrentUser()->getID(), PDO::PARAM_INT);
            $stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
            $db->beginTransaction();
            try {
                $stmt->execute();
                $db->commit();
            } catch (PDOException $e) {
                $this->logger->error("Insert client query failed. $e");
                $db->rollBack();
                throw new Exception("Failed registering new client");
            }
            return true;
        } catch (Exception $e) {
            $this->logger->error("$e");
            return false;
        }
    }

    public function countUsers($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'select count(*) from USER where CLIENT_ID=:clientID';
            $stmt = $db->prepare($query);
            $stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn(0);
            unset($stmt); //free
            return $count;
        } catch (PDOException $e) {
            $this->logger->error("$e");
            throw new Exception('Query error');
        }
    }

    public function delete($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            if ($this->countUsers($clientID) > 0)
                throw new Exception("Can not delete client which has existing user accounts");
            $query = 'delete from CLIENT where CLIENT_ID=:clientID';
            $stmt = $db->prepare($query);
            $stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
            $db->beginTransaction();
            try {
                $stmt->execute();
                $db->commit();
                unset($stmt);
            } catch (Exception $e) {
                $db->rollBack();
                unset($stmt);
                throw $e;
            }
        } catch (PDOException $e) {
            $this->logger->error("$e");
            throw new Exception('Query error');
        }
    }

    public function getDetails($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'select 
					c.CLIENT_ID as clientID,
					c.COMPANY_NAME as companyName,
					c.COMPANY_URL as companyUrl,
					c.COUNTRY_CODE as countryCode,
					cn.COUNTRY_NAME as countryName,
					c.CONTACT_NAME as contactName,
					c.CONTACT_EMAIL as contactEmail,
					c.CONTACT_PHONE as contactPhone,
					c.CREATED_BY as createdBy,
					a1.ADMIN_DISPLAYNAME as createdByName,
					c.CREATED_DATE as createdTimestamp,
					c.UPDATED_BY as updatedBy,
					a2.ADMIN_DISPLAYNAME as updatedByName,
					c.UPDATED_DATE as updatedTimestamp
				from CLIENT as c
					inner join ADMIN as a1 on c.CREATED_BY=a1.ADMIN_ID
					left join ADMIN as a2 on c.UPDATED_BY=a2.ADMIN_ID
					left join COUNTRY as cn on c.COUNTRY_CODE=cn.COUNTRY_CODE
				 where CLIENT_ID=:clientID';
            $stmt = $db->prepare($query);
            $stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
            $stmt->execute();
            $details = $stmt->fetch(PDO::FETCH_ASSOC);
            unset($stmt);
            return $details;
        } catch (Exception $e) {
            $this->logger->error("$e");
            return false;
        }
    }

    public function getBillingDetails($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'select 
					CLIENT_ID as clientID,
					BILLED_SMS as billsms, 
                                        BILLED_SMS_DESC as subIdBillNo,
                                        UNKNOWN as unknown, 
                                        PENDING as pending, 
                                        UNDELIVERED as undelivered,
                                        DELIVERED as showDelivered, 
                                        DELIVERED_DESC as deliveredDesc, 
                                        TOTAL_SMS as totalSms,
                                        TOTAL_CHARGE as totalCharge, 
                                        PROVIDER as showProvider, 
                                        PROVIDER_DESC as provided
				from BILLING_OPTIONS
				 where CLIENT_ID=:clientID';
            $stmt = $db->prepare($query);
            $stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
            $stmt->execute();
            $details = $stmt->fetch(PDO::FETCH_ASSOC);
            unset($stmt);
            return $details;
        } catch (Exception $e) {
            $this->logger->error("$e");
            return false;
        }
    }

//	public function  getDetails($clientID){
//
//	}
    public function checkExistence($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = 'select count(*) from CLIENT where CLIENT_ID=:clientID';
            $stmt = $db->prepare($query);
            $stmt->bindValue(':clientID', $clientID, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn(0);
            unset($stmt);
            return $count > 0;
        } catch (PDOException $e) {
            $this->logger->error("$e");
            throw new Exception("Query error");
        }
    }

}
