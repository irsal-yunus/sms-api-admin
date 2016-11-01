<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author irsyah.mardiah(icha)
 * @author Fathir Wafda
 * @author Dwikky Maradhiza
 */
class ApiReport extends ApiBaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getProfileClient($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = "SELECT CLIENT_ID, BILLED_SMS, UNKNOWN, PENDING, ";
            $query .= "UNDELIVERED, DELIVERED, DELIVERED_DESC, TOTAL_SMS, ";
            $query .= "TOTAL_CHARGE, PROVIDER, PROVIDER_DESC ";
            $query .= "FROM BILLING_OPTIONS ";
            $query .= "WHERE CLIENT_ID = '" . $clientID . "'";

            $list = $db->query($query)->fetch(PDO::FETCH_ASSOC);
            return $list;
        } catch (Throwable $e) {
            $this->logger->error("$e");
            throw new Exception("Query failed get Profile");
        }
    }
    
     public function getBillingClient() {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
              $query = "SELECT CLIENT_ID ";
            $query .= "FROM SMS_API_V2.CLIENT A";
            
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            
            return $list;
            
        } catch (Throwable $e) {
            $this->logger->error("$e");
	    throw new Exception("Query failed get Profile");
        }
    }          
    
    public function getDataReport($userId, $month, $year, $lastUpdated) {
        try {
            $lastUpdated = $lastUpdated === false || $lastUpdated == '' ? date("Y-m-01", strtotime("now")) : date("Y-m-d",  strtotime($lastUpdated));
            $now         = date("Y-m-d", strtotime("now"));
            $now         = date('m', strtotime($lastUpdated)) == date('m', strtotime($now)) ? $now : date("Y-m-t", strtotime($lastUpdated));
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);       
            //HARDCODE
            //$month = '03';
            //$year = '2016';
            
            $query = "SELECT 
                        MESSAGE_ID,
                        DESTINATION,
                        MESSAGE_CONTENT,
                        MESSAGE_STATUS,
                        CASE X.IS_RECREDITED
                            WHEN 0 THEN 
                                CASE X.STATUS 
                                    WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
                                    ELSE 'DELIVERED'
                                END
                            WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
                        END AS DESCRIPTION_CODE,                        
                        SEND_DATETIME,
                        SENDER,
                        USER_ID,
                        MESSAGE_COUNT,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS <>  'Undelivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
                        IF(X.IS_RECREDITED = 1, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED_UNCHARGED,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED
                        FROM
                    (SELECT 
                        B.MESSAGE_ID,
                        B.DESTINATION,
                        B.SEND_DATETIME,
                        B.MESSAGE_CONTENT, 
                        B.MESSAGE_STATUS,
                        B.SENDER,
                        B.USER_ID,
                        IF(LENGTH(B.MESSAGE_CONTENT) <= 160,
                            1,
                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)) AS MESSAGE_COUNT,
                            D.IS_RECREDITED,
                            D.STATUS,
                        B.USER_ID_NUMBER
                    FROM SMS_API_V2.USER_MESSAGE_STATUS B FORCE INDEX (`IDX_SENT_TIMESTAMP2`) 
                    INNER JOIN BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
                    WHERE
                         MONTH(B.SEND_DATETIME) = '$month' AND YEAR(B.SEND_DATETIME) = '$year'
                         AND (B.SEND_DATETIME > '$lastUpdated' AND B.SEND_DATETIME <= '$now')) AS X
                    WHERE 
                            X.IS_RECREDITED IN ('0','1') AND X.USER_ID_NUMBER = '$userId'  ORDER BY SEND_DATETIME ASC";

            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            //$this->logger->info("query = \n$query");
            return $list;
        } catch (Throwable $e) {
            $this->logger->error("$e");
            throw new Exception("Query failed get Data report: " . $e->getMessage());
        }
    }
    
    public function getDataCronReport($userId, $month = false, $year = false, $lastUpdateDate = false, $lastMonth=false) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
            
            $month     = $month === false ? date("m") : $month;
            $year      = $year  === false ? date("Y") : $year;
            $startDate = $lastUpdateDate === false || $lastUpdateDate == '' ? date("Y-m-01", strtotime("now")) : date('Y-m-d', strtotime($lastUpdateDate));

            $intDate   = (int)date('d') -3;
            if($lastMonth !== false){
                $lastDate = date('Y-m-t', strtotime("$lastUpdateDate"));
                $endDate  = $lastUpdateDate <= $lastDate ? $lastDate : date('Y-m-d', strtotime("$lastUpdateDate $intDate days"));
            }
            else{
                $endDate  = date("Y-m-d", strtotime("-2 days"));
            }
            
            //$this->logger->info("$userId| LastUpdate = $lastUpdateDate| start = $startDate ==> end = $endDate");
            $query = "SELECT 
                        MESSAGE_ID,
                        DESTINATION,
                        MESSAGE_CONTENT,
                        MESSAGE_STATUS,
                        CASE X.IS_RECREDITED
                            WHEN 0 THEN 
                                CASE X.STATUS 
                                    WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
                                    ELSE 'DELIVERED'
                                END
                            WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
                        END AS DESCRIPTION_CODE,
                        SEND_DATETIME,
                        SENDER,
                        USER_ID,
                        MESSAGE_COUNT,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS <>  'Undelivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
                        IF(X.IS_RECREDITED = 1, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED_UNCHARGED,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED
                        FROM
                    (SELECT 
                        B.MESSAGE_ID,
                        B.DESTINATION,
                        B.MESSAGE_CONTENT, B.MESSAGE_STATUS,
                        B.SEND_DATETIME,
                        B.SENDER,
                        B.USER_ID,
                        IF(LENGTH(B.MESSAGE_CONTENT) <= 160,
                            1,
                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)) AS MESSAGE_COUNT,
                            D.IS_RECREDITED,
                            D.STATUS,
                        B.USER_ID_NUMBER
                    FROM SMS_API_V2.USER_MESSAGE_STATUS B FORCE INDEX (`IDX_SENT_TIMESTAMP2`) 
                    INNER JOIN BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
                    WHERE
                        (MONTH(SEND_DATETIME) = '$month' AND YEAR(SEND_DATETIME) = '$year') AND
                        (SEND_DATETIME >= '$startDate' AND SEND_DATETIME <= '$endDate')) AS X
                    WHERE 
                            X.IS_RECREDITED IN ('0','1') AND X.USER_ID = '$userId' ORDER BY SEND_DATETIME ASC";

            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
                        
            return $list;
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            throw new Exception("Query failed get Data report: " . $e->getMessage());
        }
    }
    
    public function getUser($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);

            $query = "SELECT USER_NAME FROM USER WHERE CLIENT_ID = '" . $clientID . "' AND ACTIVE = TRUE ";
            //echo "$query\n";
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

            return $list;
        } catch (Throwable $e) {
            $this->logger->error("getUser errpr: ".$e->getMessage());
            throw new Exception("Query failed get User");
        }
    }

    public function getHeader($userId, $billedSMS, $errorCode, $deliveredDesc) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);

            $query = "SELECT C.MESSAGE_STATUS, sum(C.MESSAGE_COUNT) AS
                        MESSAGE COUNT FROM (SELECT B.DESTINATION, ";
            if ($errorCode == false) {
                if ($deliveredDesc != "") {
                    $query .= " CASE D.STATUS ";
                    $pieces = explode(";", $deliveredDesc);
                    foreach ($pieces as $value) {
                        $query .= "WHEN '" . $value . "' THEN 'Delivered' ";
                    }

                    $query .= " else D. STATUS end as MESSAGE_STATUS, ";
                } else {
                    $query .= " D. STATUS AS MESSAGE_STATUS, ";
                }
            } else {
                $query .= " B.MESSAGE_STATUS, ";
            }


            $query .= " B.SEND_DATETIME, B.SENDER,
                        B.USER_ID,

                        if(length(B.MESSAGE_CONTENT)<=160,1,ceiling(length(B.MESSAGE_CONTENT)/153))
                        as MESSAGE_COUNT ";
            $query .= " FROM SMS_API_V2.USER_MESSAGE_STATUS B,
                        BILL_U_MESSAGE.DELIVERY_STATUS D "
                    . " WHERE B.MESSAGE_STATUS = D.ERROR_CODE ";
            $query .= " AND D.IS_RECREDITED in (";

            if ($billedSMS = 1 || $billedSMS = 3) {
                $query .= "'0'";
                if ($billedSMS = 3) {
                    $query .= ",";
                }
            }

            if ($billedSMS = 2 || $billedSMS = 3) {
                $query .= "'1'";
            }

            $query .= ") AND B.USER_ID = '" . $userId . "') C GROUP BY C.MESSAGE_STATUS";

            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

            return $list;
        } catch (Throwable $e) {
            $this->logger->error("getHeader error: ".$e->getMessage());
            //echo $e;
            throw new Exception("Query failed get Header");
        }
    }

    public function getHeaderProvider($userId, $billedSMS) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);

            $query = "SELECT C.DESTINATION, sum(C.MESSAGE_COUNT) AS MESSAGE COUNT FROM "
                    . "(SELECT CASE SUBSTRING(B.DESTINATION,1,5)"
                    . "  WHEN '62811' THEN 'TSEL'"
                    . "  WHEN '62812' THEN 'TSEL'"
                    . "  WHEN '62813' THEN 'TSEL'"
                    . "  WHEN '62821' THEN 'TSEL'"
                    . "  WHEN '62822' THEN 'TSEL'"
                    . "  WHEN '62823' THEN 'TSEL'"
                    . "  WHEN '62851' THEN 'TSEL'"
                    . "  WHEN '62852' THEN 'TSEL'"
                    . "  WHEN '62853' THEN 'TSEL'"
                    . "  ELSE 'NON-TSEL'"
                    . "  END AS DESTINATION, ";

            $query .= " if(length(B.MESSAGE_CONTENT)<=160,1,ceiling(length(B.MESSAGE_CONTENT)/153)) as MESSAGE_COUNT ";
            $query .= " FROM SMS_API_V2.USER_MESSAGE_STATUS B, BILL_U_MESSAGE.DELIVERY_STATUS D "
                    . " WHERE B.MESSAGE_STATUS = D.ERROR_CODE ";
            $query .= " AND D.IS_RECREDITED in (";

            if ($billedSMS = 1 || $billedSMS = 3) {
                $query .= "'0'";
                if ($billedSMS = 3) {
                    $query .= ",";
                }
            }

            if ($billedSMS = 2 || $billedSMS = 3) {
                $query .= "'1'";
            }

            $query .= ") AND B.USER_ID = '" . $userId . "') C "
                    . "GROUP BY C.DESTINATION ORDER BY C.DESTINATION DESC";

            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

            return $list;
        } catch (Throwable $e) {
            $this->logger->error("getHeaderProvider error: ".$e->getMessage());
            //echo $e;
            throw new Exception("Query failed get Header Provider");
        }
    }

}
