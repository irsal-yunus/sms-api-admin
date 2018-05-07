<?php

/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once dirname(__DIR__).'/FirePHPCore/FirePHP.class.php';

/**
 * Description of SmsApiAccount
 *
 * @author setia.budi
 *
 * @author Fathir Wafda --> add insertBilling, updateBilling and getBillingDetail
 *
 * @author Ayu Musfita  --> delete insertBilling, updateBilling and getBillingDetail    tracker 22936
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
                        CREATED_BY, CREATED_DATE, CUSTOMER_ID,
                        CONTACT_ADDRESS
                        )
                    values (
                        :companyName, :companyUrl, :countryCode,
                        :contactName, :contactEmail, :contactPhone,
                        :adminID, now(), :customerId, :contactAddress
                        )';
            $adminID = SmsApiAdmin::getCurrentUser()->getID();
            $stmt = $db->prepare($query);
            $stmt->bindValue(':customerId', $data['customerId'], PDO::PARAM_STR);
            $stmt->bindValue(':companyName', $data['companyName'], PDO::PARAM_STR);
            $stmt->bindValue(':companyUrl', $data['companyUrl'], PDO::PARAM_STR);
            $stmt->bindValue(':countryCode', $data['countryCode'], PDO::PARAM_STR);
            $stmt->bindValue(':contactName', $data['contactName'], PDO::PARAM_STR);
            $stmt->bindValue(':contactEmail', $data['contactEmail'], PDO::PARAM_STR);
            $stmt->bindValue(':contactPhone', $data['contactPhone'], PDO::PARAM_STR);
            $stmt->bindValue(':contactAddress', $data['contactAddress'], PDO::PARAM_STR);
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

    public function update($clientID, $updates) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $fields = array(
                'customerId' => array(':customerId', 'CUSTOMER_ID', PDO::PARAM_STR),
                'companyName' => array(':companyName', 'COMPANY_NAME', PDO::PARAM_STR),
                'companyUrl' => array(':companyUrl', 'COMPANY_URL', PDO::PARAM_STR),
                'countryCode' => array(':countryCode', 'COUNTRY_CODE', PDO::PARAM_STR),
                'contactName' => array(':contactName', 'CONTACT_NAME', PDO::PARAM_STR),
                'contactEmail' => array(':contactEmail', 'CONTACT_EMAIL', PDO::PARAM_STR),
                'contactPhone' => array(':contactPhone', 'CONTACT_PHONE', PDO::PARAM_STR),
                'contactAddress' => array(':contactAddress', 'CONTACT_ADDRESS', PDO::PARAM_STR),
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
                    c.CUSTOMER_ID as customerId,
                    c.COMPANY_URL as companyUrl,
                    c.COUNTRY_CODE as countryCode,
                    cn.COUNTRY_NAME as countryName,
                    c.CONTACT_NAME as contactName,
                    c.CONTACT_EMAIL as contactEmail,
                    c.CONTACT_PHONE as contactPhone,
                    c.CONTACT_ADDRESS as contactAddress,
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
