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
            $query .= "FROM SMS_API_V21.CLIENT A";
//            $query .= "FROM SMS_API_V21.CLIENT A";
            
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
            
            
            // HARDCODE
            // ===================
            // $month = '03';
            // $year  = '2016';
            

            /**
             * =================================================================
             *              NEW QUERY WITH PRICE
             * =================================================================
             */
//            $query = "SELECT 
//                        MESSAGE_ID,
//                        DESTINATION,
//                        MESSAGE_CONTENT,
//                        MESSAGE_STATUS,
//                        CASE X.IS_RECREDITED
//                            WHEN 0 THEN 
//                                CASE X.STATUS 
//                                    WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
//                                    ELSE 'DELIVERED'
//                                END
//                            WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
//                        END AS DESCRIPTION_CODE,                        
//                        SEND_DATETIME,
//                        SENDER,
//                        USER_ID,
//                        MESSAGE_COUNT,
//                        IF(X.IS_RECREDITED = 0, IF(X.STATUS <>  'Undelivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
//                        IF(X.IS_RECREDITED = 1, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED_UNCHARGED,
//                        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED
//                        FROM
//                    (SELECT 
//                        B.MESSAGE_ID,
//                        B.DESTINATION,
//                        B.SEND_DATETIME,
//                        B.MESSAGE_CONTENT, 
//                        B.MESSAGE_STATUS,
//                        B.SENDER,
//                        B.USER_ID,
//                        IF(LENGTH(B.MESSAGE_CONTENT) <= 160,
//                            1,
//                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)) AS MESSAGE_COUNT,
//                            D.IS_RECREDITED,
//                            D.STATUS,
//                        B.USER_ID_NUMBER
//                    FROM SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (`IDX_SENT_TIMESTAMP`) 
//                    INNER JOIN BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
//                    WHERE
//                         MONTH(B.SEND_DATETIME) = '$month' AND YEAR(B.SEND_DATETIME) = '$year'
//                         AND (B.SEND_DATETIME > '$lastUpdated 23:59:59' AND B.SEND_DATETIME <= '$now 23:59:59')) AS X
//                    WHERE 
//                            X.IS_RECREDITED IN ('0','1') AND X.USER_ID_NUMBER = '$userId'  ORDER BY SEND_DATETIME ASC";

            
            
            
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
                            IFNULL(
                                OP_ID, 
                                'DEFAULT'
                            ) AS OPERATOR,
                            CASE X.IS_RECREDITED
                                    WHEN 1 THEN '0'
                                    ELSE IFNULL(    PER_SMS_PRICE, 
                                                        (   
                                                                SELECT      BPMX.PER_SMS_PRICE 
                                                                FROM        BILL_PRICELIST.BILLING_PROFILE_MAP AS BPMX, 
                                                                            SMS_API_V21.USER AS USRX
                                                                WHERE       BPMX.OP_ID = 'DEFAULT' 
                                                                            AND BPMX.BILLING_PROFILE_ID = USRX.BILLING_PROFILE_ID
                                                                GROUP BY    PER_SMS_PRICE
                                                        )
                                                ) * MESSAGE_COUNT
                            END AS PRICE,
                            IF(
                                X.IS_RECREDITED = 0,
                                IF(
                                    X.STATUS <> 'Undelivered',
                                    MESSAGE_COUNT,
                                    0
                                ),
                                0
                            ) AS DELIVERED,
                            IF(
                                X.IS_RECREDITED = 1,
                                IF(
                                    X.STATUS = 'Undelivered',
                                    MESSAGE_COUNT,
                                    0
                                ),
                                0
                            ) AS UNDELIVERED_UNCHARGED,
                            IF(
                                X.IS_RECREDITED = 0,
                                IF(
                                    X.STATUS = 'Undelivered',
                                    MESSAGE_COUNT,
                                    0
                                ),
                                0
                            ) AS UNDELIVERED
                        FROM 
                            (  
                                    SELECT 
                                            B.MESSAGE_ID,
                                            B.DESTINATION,
                                            B.MESSAGE_CONTENT,
                                            B.MESSAGE_STATUS,
                                            B.SEND_DATETIME,
                                            B.SENDER,
                                            B.USER_ID,
                                            IF(
                                                    LENGTH(B.MESSAGE_CONTENT) <= 160, 1, 
                                                    CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)
                                            ) AS MESSAGE_COUNT,
                                            D.IS_RECREDITED,
                                            D.STATUS,
                                            B.USER_ID_NUMBER
                                    FROM 
                                        SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (IDX_SENT_TIMESTAMP)
                                    INNER JOIN 
                                        BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
                                    WHERE   (
                                                    MONTH(SEND_DATETIME) = '$month'
                                                    AND YEAR(SEND_DATETIME) = '$year'
                                            )
                                            AND (
                                                    SEND_DATETIME > '$lastUpdated 23:59:59' 
                                                    AND SEND_DATETIME <= '$now 23:59:59'
                                            )
                            ) AS X
                            LEFT JOIN ( 
                                    SELECT 
                                            IFNULL(
                                                    SUBSTRING(OP_DIAL_RANGE_LOWER, 1, LOCATE('00', OP_DIAL_RANGE_LOWER) - 1),
                                                    (
                                                            SELECT  ODP2.OP_ID 
                                                            FROM    First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP2 
                                                            WHERE   ODP2.OP_ID = 'DEFAULT'
                                                    )
                                            ) AS DESTINATION_PREFIX,
                                            IF( ODP.OP_ID IN (
                                                            SELECT  BPM2.OP_ID 
                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM2 
                                                            WHERE   BPM2.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
                                                    ),
                                                    ODP.OP_ID,
                                                    'DEFAULT'
                                            ) AS OP_ID,
                                            IF( ODP.OP_ID IN (
                                                            SELECT  BPM3.OP_ID 
                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM3 
                                                            WHERE   BPM3.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
                                                    ),
                                                    (
                                                            SELECT  BPM4.PER_SMS_PRICE 
                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM4 
                                                            WHERE   BPM4.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
                                                                    AND BPM4.OP_ID = ODP.OP_ID
                                                    ),
                                                    (
                                                            SELECT  BPM5.PER_SMS_PRICE 
                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM5
                                                            WHERE   BPM5.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
                                                                    AND BPM5.OP_ID = 'DEFAULT'
                                                    )
                                            ) AS PER_SMS_PRICE
                                    FROM
                                            First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP, 
                                            BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM, 
                                            SMS_API_V21.USER AS USR
                                    WHERE
                                            USR.USER_ID = '$userId'
                                            AND BPM.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
                                    GROUP BY DESTINATION_PREFIX
                        ) AS SMS_PRICE 
                                ON  SUBSTRING(DESTINATION, 1, LENGTH(DESTINATION_PREFIX)) = DESTINATION_PREFIX 
                                AND DESTINATION_PREFIX <> '' 
                        WHERE
                                X.IS_RECREDITED IN ('0' , '1') 
                                AND X.USER_ID_NUMBER = '$userId'
                        GROUP BY X.MESSAGE_ID
                        ORDER BY SEND_DATETIME ASC
            ";

            
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            //$this->logger->info("query = \n$query");
            return $list;
        } 
        catch (Throwable $e) {
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
            
            
            // HARDCODE
            // ===================
            // $month     = '04';
            // $year      = '2017';
            // $startDate = '2017-04-01';

            $intDate   = (int)date('d') -3;
            if($lastMonth !== false) {
                $lastDate = date('Y-m-t', strtotime("$lastUpdateDate"));
                $endDate  = $lastUpdateDate <= $lastDate ? $lastDate : date('Y-m-d', strtotime("$lastUpdateDate $intDate days"));
            }
            else {
                $endDate  = date("Y-m-d", strtotime("-2 days"));
            }

            
            /**
             * =================================================================
             *              OLD QUERY WITHOUT PRICE
             * =================================================================
             */
            //$this->logger->info("$userId| LastUpdate = $lastUpdateDate| start = $startDate ==> end = $endDate");
//            $query = "SELECT 
//                        MESSAGE_ID,
//                        DESTINATION,
//                        MESSAGE_CONTENT,
//                        MESSAGE_STATUS,
//                        CASE X.IS_RECREDITED
//                            WHEN 0 THEN 
//                                CASE X.STATUS 
//                                    WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
//                                    ELSE 'DELIVERED'
//                                END
//                            WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
//                        END AS DESCRIPTION_CODE,
//                        SEND_DATETIME,
//                        SENDER,
//                        USER_ID,
//                        MESSAGE_COUNT,
//                        IF(X.IS_RECREDITED = 0, IF(X.STATUS <>  'Undelivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
//                        IF(X.IS_RECREDITED = 1, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED_UNCHARGED,
//                        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED
//                        FROM
//                    (SELECT 
//                        B.MESSAGE_ID,
//                        B.DESTINATION,
//                        B.MESSAGE_CONTENT, B.MESSAGE_STATUS,
//                        B.SEND_DATETIME,
//                        B.SENDER,
//                        B.USER_ID,
//                        IF(LENGTH(B.MESSAGE_CONTENT) <= 160,
//                            1,
//                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)) AS MESSAGE_COUNT,
//                            D.IS_RECREDITED,
//                            D.STATUS,
//                        B.USER_ID_NUMBER
//                    FROM SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (`IDX_SENT_TIMESTAMP`) 
//                    INNER JOIN BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
//                    WHERE
//                        (MONTH(SEND_DATETIME) = '$month' AND YEAR(SEND_DATETIME) = '$year') AND
//                        (SEND_DATETIME >= '$startDate 00:00:00' AND SEND_DATETIME < '$endDate 00:00:00')) AS X
//                    WHERE 
//                            X.IS_RECREDITED IN ('0','1') AND X.USER_ID = '$userId' ORDER BY SEND_DATETIME ASC";

            
            
            /**
             * =================================================================
             *              NEW QUERY WITH PRICE
             * =================================================================
             */
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
                            IFNULL(
                                OP_ID, 
                                'DEFAULT'
                            ) AS OPERATOR,
                            CASE X.IS_RECREDITED
                                    WHEN 1 THEN '0'
                                    ELSE IFNULL(    PER_SMS_PRICE, 
                                                        (   
                                                                SELECT      BPMX.PER_SMS_PRICE 
                                                                FROM        BILL_PRICELIST.BILLING_PROFILE_MAP AS BPMX, 
                                                                            SMS_API_V21.USER AS USRX
                                                                WHERE       BPMX.OP_ID = 'DEFAULT' 
                                                                            AND BPMX.BILLING_PROFILE_ID = USRX.BILLING_PROFILE_ID
                                                                GROUP BY    PER_SMS_PRICE
                                                        )
                                                ) * MESSAGE_COUNT
                            END AS PRICE,
                            IF(
                                X.IS_RECREDITED = 0,
                                IF(
                                    X.STATUS <> 'Undelivered',
                                    MESSAGE_COUNT,
                                    0
                                ),
                                0
                            ) AS DELIVERED,
                            IF(
                                X.IS_RECREDITED = 1,
                                IF(
                                    X.STATUS = 'Undelivered',
                                    MESSAGE_COUNT,
                                    0
                                ),
                                0
                            ) AS UNDELIVERED_UNCHARGED,
                            IF(
                                X.IS_RECREDITED = 0,
                                IF(
                                    X.STATUS = 'Undelivered',
                                    MESSAGE_COUNT,
                                    0
                                ),
                                0
                            ) AS UNDELIVERED
                        FROM (  
                                SELECT 
                                        B.MESSAGE_ID,
                                        B.DESTINATION,
                                        B.MESSAGE_CONTENT,
                                        B.MESSAGE_STATUS,
                                        B.SEND_DATETIME,
                                        B.SENDER,
                                        B.USER_ID,
                                        IF(
                                            LENGTH(B.MESSAGE_CONTENT) <= 160, 
                                            1, 
                                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)
                                        ) AS MESSAGE_COUNT,
                                        D.IS_RECREDITED,
                                        D.STATUS,
                                        B.USER_ID_NUMBER
                                FROM 
                                    SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (IDX_SENT_TIMESTAMP)
                                INNER JOIN 
                                    BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
                                WHERE   (
                                            MONTH(SEND_DATETIME) = '$month'
                                            AND YEAR(SEND_DATETIME) = '$year'
                                        )
                                        AND (
                                            SEND_DATETIME >= '$startDate 00:00:00' 
                                            AND SEND_DATETIME < '$endDate 00:00:00'
                                        )
                                ) AS X
                                LEFT JOIN ( SELECT 
                                                    IFNULL(
                                                            SUBSTRING(OP_DIAL_RANGE_LOWER, 1, LOCATE('00', OP_DIAL_RANGE_LOWER) - 1),
                                                            (
                                                                SELECT  ODP2.OP_ID 
                                                                FROM    First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP2 
                                                                WHERE   ODP2.OP_ID = 'DEFAULT'
                                                            )
                                                    ) AS DESTINATION_PREFIX,
                                                    IF( ODP.OP_ID IN (
                                                                SELECT  BPM2.OP_ID 
                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM2 
                                                                WHERE   BPM2.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
                                                            ),
                                                            ODP.OP_ID,
                                                            'DEFAULT'
                                                    ) AS OP_ID,
                                                    IF( ODP.OP_ID IN (
                                                                SELECT  BPM3.OP_ID 
                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM3 
                                                                WHERE   BPM3.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
                                                            ),
                                                            (
                                                                SELECT  BPM4.PER_SMS_PRICE 
                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM4 
                                                                WHERE   BPM4.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
                                                                        AND BPM4.OP_ID = ODP.OP_ID
                                                            ),
                                                            (
                                                                SELECT  BPM5.PER_SMS_PRICE 
                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM5
                                                                WHERE   BPM5.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
                                                                        AND BPM5.OP_ID = 'DEFAULT'
                                                            )
                                                    ) AS PER_SMS_PRICE
                                            FROM
                                                    First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP, 
                                                    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM, 
                                                    SMS_API_V21.USER AS USR
                                            WHERE
                                                    USR.USER_NAME = '$userId'
                                                    AND BPM.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
                                            GROUP BY DESTINATION_PREFIX
                                ) AS SMS_PRICE 
                                        ON  SUBSTRING(DESTINATION, 1, LENGTH(DESTINATION_PREFIX)) = DESTINATION_PREFIX 
                                        AND DESTINATION_PREFIX <> '' 
                        WHERE
                                X.IS_RECREDITED IN ('0' , '1') 
                                AND X.USER_ID = '$userId'
                        GROUP BY X.MESSAGE_ID
                        ORDER BY SEND_DATETIME ASC
            ";
            
            
//            if($userId == 'PEPTrial'){
//                die($query);
//            }
            
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
                        
            return $list;
        } 
        catch (Throwable $e) {
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
            $query .= " FROM SMS_API_V21.USER_MESSAGE_STATUS B,
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
            $query .= " FROM SMS_API_V21.USER_MESSAGE_STATUS B, BILL_U_MESSAGE.DELIVERY_STATUS D "
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
            throw new Exception("Query failed get Header Provider");
        }
    }

    
    
}
