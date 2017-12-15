<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author irsyah.mardiah(icha)
 * @author Fathir Wafda
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
        } catch (Exception $e) {
            $this->logger->error("$e");
            throw new Exception("Query failed get Profile");
        }
    }
    
     public function getBillingClient() {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            $query = "SELECT CLIENT_ID, BILLED_SMS, UNKNOWN, PENDING, ";
            $query .= "UNDELIVERED, DELIVERED, DELIVERED_DESC, TOTAL_SMS, ";
            $query .= "TOTAL_CHARGE, PROVIDER, PROVIDER_DESC ";
            $query .= "FROM SMS_API_V2.BILLING_OPTIONS A";
            
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            
            return $list;
            
        } catch (Exception $e) {
            $this->logger->error("$e");
	    throw new Exception("Query failed get Profile");
        }
    }          
    

//    public function getDataReport($userId, $billedSMS, $errorCode, $deliveredDesc, $startDate = '', $endDate = '') {
//        try {
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
//
//            $query = "SELECT B.MESSAGE_ID, B.DESTINATION, B.MESSAGE_CONTENT, ";
//            if ($errorCode == true) {
////                if ($deliveredDesc != "") {
////                    $query .= " CASE D.STATUS ";
////                    $pieces = explode(";", $deliveredDesc);
////                    foreach ($pieces as $value) {
////                        $query .= "WHEN '" . $value . "' THEN 'Delivered' ";
////                    }
////
////                    $query .= " else D. STATUS end as MESSAGE_STATUS, ";
////                } else {
////                    $query .= " D. STATUS AS MESSAGE_STATUS, ";
////                }
////            } else {
////                $query .= " B.MESSAGE_STATUS, ";
////            }
//                $query .= " D.STATUS, ";
//            } 
//
//
//            $query .= " B.MESSAGE_STATUS, B.SEND_DATETIME, B.SENDER, 
//                        B.USER_ID,
//                        if(length(B.MESSAGE_CONTENT)<=160,1,ceiling(length(B.MESSAGE_CONTENT)/153)) as MESSAGE_COUNT ";
//            $query .= " FROM SMS_API_V2.USER_MESSAGE_STATUS B, BILL_U_MESSAGE.DELIVERY_STATUS D "
//                    . " WHERE B.MESSAGE_STATUS = D.ERROR_CODE ";
//            $query .= " AND D.IS_RECREDITED in (";
//
//            if ($billedSMS == 1 || $billedSMS == 3) {
//                $query .= "'0'";
//                if ($billedSMS == 3) {
//                    $query .= ",";
//                }
//            }
//            if ($billedSMS == 2 || $billedSMS == 3) {
//                $query .= "'1'";
//            }
//
//            // if not emtpy use param value from date input
//            // otherwise use this
//            if (empty($startDate)) {
//
//                $startDate1 = date("Y-m-d", strtotime("-5 months"));
//                $startDate = $startDate1 . " 17:00:00";
//            }
//
//            if (empty($endDate)) {
//
//                $endDate1 = date("Y-m-d", strtotime("0 months"));
//                $endDate = $endDate1 . " 16:59:59";
//            }
//
//
//            $query .= ") AND B.USER_ID_NUMBER = '" . $userId . "' AND (SEND_DATETIME >= '" . $startDate . "' AND SEND_DATETIME <= '" . $endDate . "') ORDER BY MESSAGE_ID DESC";
//
//            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
//            error_log($query);
//            return $list;
//        } catch (Exception $e) {
//            $this->logger->error("$e");
//            echo $e;
//            throw new Exception("Query failed get Data report");
//        }
//    }
    
    public function getDataReport($userId, $billedSMS, $errorCode, $deliveredDesc, $startDate = '', $endDate = '') {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
            
            // if not emtpy use param value from date input
            // otherwise use this
            if (empty($startDate)) {

                $startDate1 = date("Y-m-d", strtotime("-6 days"));
                $startDate = $startDate1 . " 17:00:00";
            }

            if (empty($endDate)) {

                $endDate1 = date("Y-m-d", strtotime("-3 days"));
                $endDate = $endDate1 . " 16:59:59";
            }

            $query = "SELECT 
                        MESSAGE_ID,
                        DESTINATION,
                        MESSAGE_CONTENT, ";
            
            if($errorCode == true){
                $query .= "MESSAGE_STATUS, ";  
            }
            $query .= "SEND_DATETIME,
                        SENDER,
                        USER_ID,
                        MESSAGE_COUNT,
                        IF(X.IS_RECREDITED = 1, MESSAGE_COUNT, 0) AS UNCHARGED,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS = 'Delivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS = 'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED,
                        (SELECT CASE SUBSTRING(DESTINATION,1,5)"
                        . "  WHEN '62811' THEN MESSAGE_COUNT"
                        . "  WHEN '62812' THEN MESSAGE_COUNT"
                        . "  WHEN '62813' THEN MESSAGE_COUNT"
                        . "  WHEN '62821' THEN MESSAGE_COUNT"
                        . "  WHEN '62822' THEN MESSAGE_COUNT"
                        . "  WHEN '62823' THEN MESSAGE_COUNT"
                        . "  WHEN '62851' THEN MESSAGE_COUNT"
                        . "  WHEN '62852' THEN MESSAGE_COUNT"
                        . "  WHEN '62853' THEN MESSAGE_COUNT"
                        . "  ELSE 0"
                        . "  END) AS TSEL,
                        (SELECT CASE SUBSTRING(DESTINATION,1,5)"
                        . "  WHEN '62811' THEN MESSAGE_COUNT"
                        . "  WHEN '62812' THEN MESSAGE_COUNT"
                        . "  WHEN '62813' THEN MESSAGE_COUNT"
                        . "  WHEN '62821' THEN MESSAGE_COUNT"
                        . "  WHEN '62822' THEN MESSAGE_COUNT"
                        . "  WHEN '62823' THEN MESSAGE_COUNT"
                        . "  WHEN '62851' THEN MESSAGE_COUNT"
                        . "  WHEN '62852' THEN MESSAGE_COUNT"
                        . "  WHEN '62853' THEN MESSAGE_COUNT"
                        . "  ELSE 0"
                        . "  END) AS TSEL,
                        (SELECT CASE SUBSTRING(DESTINATION,1,5)"
                        . "  WHEN '62811' THEN 0"
                        . "  WHEN '62812' THEN 0"
                        . "  WHEN '62813' THEN 0"
                        . "  WHEN '62821' THEN 0"
                        . "  WHEN '62822' THEN 0"
                        . "  WHEN '62823' THEN 0"
                        . "  WHEN '62851' THEN 0"
                        . "  WHEN '62852' THEN 0"
                        . "  WHEN '62853' THEN 0"
                        . "  ELSE MESSAGE_COUNT"
                        . "  END) AS NON_TSEL
                        FROM
                    (SELECT 
                        B.MESSAGE_ID,
                        B.DESTINATION,
                        B.MESSAGE_CONTENT, ";
            
            if($errorCode == true){
                $query .= "B.MESSAGE_STATUS,";  
            }
            
            $query .= "B.SEND_DATETIME,
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
                        (SEND_DATETIME >= '{$startDate}' AND SEND_DATETIME <= '{$endDate}')) AS X
                    WHERE 
                            X.IS_RECREDITED IN ("; 
            
            if ($billedSMS == 1 || $billedSMS == 3) {
                $query .= "'0'";
                if ($billedSMS == 3) {
                    $query .= ",";
                }
            }
            if ($billedSMS == 2 || $billedSMS == 3) {
                $query .= "'1'";
            }
            
            $query .= ") AND X.USER_ID_NUMBER = '{$userId}'";
            error_log($query);
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            
            return $list;
        } catch (Exception $e) {
            $this->logger->error("$e");
            throw new Exception("Query failed get Data report: " . $e->getMessage());
        }
    }
    
    public function getDataCronReport($userId, $billedSMS, $errorCode, $deliveredDesc, $startDate = '', $endDate = '') {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
            
            // if not emtpy use param value from date input
            // otherwise use this
            if (empty($startDate)) {

                $startDate1 = date("Y-m-d", strtotime("-3 days"));
                $startDate = $startDate1 ;
            }

            if (empty($endDate)) {

                $endDate1 = date("Y-m-d", strtotime("0 days"));
                $endDate = $endDate1;
            }

            $query = "SELECT 
                        MESSAGE_ID,
                        DESTINATION,
                        MESSAGE_CONTENT, ";
            
            if($errorCode == true){
                $query .= "MESSAGE_STATUS, ";  
            }
            $query .= "SEND_DATETIME,
                        SENDER,
                        USER_ID,
                        MESSAGE_COUNT,
                        IF(X.IS_RECREDITED = 1, MESSAGE_COUNT, 0) AS UNCHARGED,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS = 'Delivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
                        IF(X.IS_RECREDITED = 0, IF(X.STATUS = 'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED,
                        (SELECT CASE SUBSTRING(DESTINATION,1,5)"
                        . "  WHEN '62811' THEN MESSAGE_COUNT"
                        . "  WHEN '62812' THEN MESSAGE_COUNT"
                        . "  WHEN '62813' THEN MESSAGE_COUNT"
                        . "  WHEN '62821' THEN MESSAGE_COUNT"
                        . "  WHEN '62822' THEN MESSAGE_COUNT"
                        . "  WHEN '62823' THEN MESSAGE_COUNT"
                        . "  WHEN '62851' THEN MESSAGE_COUNT"
                        . "  WHEN '62852' THEN MESSAGE_COUNT"
                        . "  WHEN '62853' THEN MESSAGE_COUNT"
                        . "  ELSE 0"
                        . "  END) AS TSEL,
                        (SELECT CASE SUBSTRING(DESTINATION,1,5)"
                        . "  WHEN '62811' THEN MESSAGE_COUNT"
                        . "  WHEN '62812' THEN MESSAGE_COUNT"
                        . "  WHEN '62813' THEN MESSAGE_COUNT"
                        . "  WHEN '62821' THEN MESSAGE_COUNT"
                        . "  WHEN '62822' THEN MESSAGE_COUNT"
                        . "  WHEN '62823' THEN MESSAGE_COUNT"
                        . "  WHEN '62851' THEN MESSAGE_COUNT"
                        . "  WHEN '62852' THEN MESSAGE_COUNT"
                        . "  WHEN '62853' THEN MESSAGE_COUNT"
                        . "  ELSE 0"
                        . "  END) AS TSEL,
                        (SELECT CASE SUBSTRING(DESTINATION,1,5)"
                        . "  WHEN '62811' THEN 0"
                        . "  WHEN '62812' THEN 0"
                        . "  WHEN '62813' THEN 0"
                        . "  WHEN '62821' THEN 0"
                        . "  WHEN '62822' THEN 0"
                        . "  WHEN '62823' THEN 0"
                        . "  WHEN '62851' THEN 0"
                        . "  WHEN '62852' THEN 0"
                        . "  WHEN '62853' THEN 0"
                        . "  ELSE MESSAGE_COUNT"
                        . "  END) AS NON_TSEL
                        FROM
                    (SELECT 
                        B.MESSAGE_ID,
                        B.DESTINATION,
                        B.MESSAGE_CONTENT, ";
            
            if($errorCode == true){
                $query .= "B.MESSAGE_STATUS,";  
            }
            
            $query .= "B.SEND_DATETIME,
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
                        (SEND_DATETIME >= '{$startDate}' AND SEND_DATETIME <= '{$endDate}')) AS X
                    WHERE 
                            X.IS_RECREDITED IN ("; 
            
            if ($billedSMS == 1 || $billedSMS == 3) {
                $query .= "'0'";
                if ($billedSMS == 3) {
                    $query .= ",";
                }
            }
            if ($billedSMS == 2 || $billedSMS == 3) {
                $query .= "'1'";
            }
            
            $query .= ") AND X.USER_ID = '{$userId}'";
            error_log($query);
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            
            return $list;
        } catch (Exception $e) {
            $this->logger->error("$e");
            throw new Exception("Query failed get Data report: " . $e->getMessage());
        }
    }

    public function getUser($clientID) {
        try {
            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);

            $query = "SELECT USER_NAME FROM USER WHERE CLIENT_ID = '" . $clientID . "' AND ACTIVE = TRUE ";

            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

            return $list;
        } catch (Exception $e) {
            $this->logger->error("$e");
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
        } catch (Exception $e) {
            $this->logger->error("$e");
            echo $e;
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
        } catch (Exception $e) {
            $this->logger->error("$e");
            echo $e;
            throw new Exception("Query failed get Header Provider");
        }
    }

}
