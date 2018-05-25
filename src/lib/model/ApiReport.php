<?php
/**
 * @author Basri.Yasin
 * @author Ayu Musfita
 *
 * Copyright(c) 2017 1rstWAP. All rights reserved.
 * -----------------------------------------------
 * #18802   2017-05-08  Basri.Y     [SMS Billing Report] Improve Performance & Tiering
 * #19517
 */

require_once dirname(dirname(__DIR__)).'/configs/config.php';
require_once dirname(dirname(__DIR__)).'/classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once dirname(dirname(__DIR__)).'/classes/PHPExcel.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;


class ApiReport {

    /**
     * Type of allowd Billing Profiles
     */
    const   BILLING_OPERATOR_BASE           = 'OPERATOR';
    const   BILLING_FIXED_BASE              = 'FIXED';
    const   BILLING_TIERING_BASE            = 'TIERING';


    /**
     * SMS Status for Query where clause
     */
    const   SMS_STATUS_ALL                  = 'STATUS_ALL';
    const   SMS_STATUS_CHARGED              = 'STATUS_CHARGED';
    const   SMS_STATUS_UNCHARGED            = 'STATUS_UNCHARGED';


    /**
     * SMS Status which displayed to Report's file
     */
    const   SMS_STATUS_DELIVERED            = 'DELIVERED';
    const   SMS_STATUS_UNDELIVERED_CHARGED  = 'UNDELIVERED (CHARGED)';
    const   SMS_STATUS_UNDELIVERED          = 'UNDELIVERED (UNCHARGED)';
    const   SMS_STATUS_UNDEFINED            = 'UNDELIVERED (CHARGED)';


    /**
     * SMS encoding type, affect the way to calculate SMS Length
     */
    const   SMS_TYPE_GSM_7BIT               = 'GSM_7BIT';
    const   SMS_TYPE_UNICODE                = 'UNICODE';

    const   OPERATOR_INDONESIA              = ['1RSTWAP',  'AXIS', 'CDMA_ID','CERIA','ESIA','EXCELCOM', 'HEPI','IM3',  'LIPPO', 'MOBILE_8','PSN',
                                               'SATELINDO','SMART','STARONE','TELKOMMOBILE','TELKOMSEL','TELKOM_FLEXI','THREE'];


    /**
     * SMS Legth by for every type
     */
    const   GSM_7BIT_SINGLE_SMS             = 160;
    const   GSM_7BIT_MULTIPLE_SMS           = 153;
    const   UNICODE_SINGLE_SMS              = 70;
    const   UNICODE_MULTIPLE_SMS            = 67;


    /**
     * Default value for undefined operator
     */
    const   DEFAULT_OPERATOR                = 'DEFAULT';


    /**
     * Cache file name
     */
    const   CACHE_LAST_DATE                 = 'lastSendDateTime.lfu',
            CACHE_BILLING_PROFILE           = 'billingProfiles.lfu',
            CACHE_REPORT_GROUP              = 'reportGroups.lfu',
            ALL_REPORT_PACKAGE              = 'BILLING_REPORT';


    /**
     * Query mode
     */
    const   QUERY_ALL                       = '',
            QUERY_SINGLE_ROW                = 'SINGLE_ROW',
            QUERY_SINGLE_COLUMN             = 'SINGLE_COLUMN',
            QUERY_SINGLE_ROW_AND_COLUMN     = 'SINGLE_ROW_AND_COLUMN';


    /**
     * Directory and file prefix and suffix
     */
    const   DIR_FINAL_REPORT                = 'FINAL_STATUS',
            DIR_AWAITING_REPORT             = 'INCLUDE_AWAITING_DR',
            SUFFIX_FINAL_REPORT             = '',
            SUFFIX_AWAITING_REPORT          = '_Include_Awaiting_Dr',
            SUFFIX_SUMMARY_FINAL_REPORT     = '_Summary',
            SUFFIX_SUMMARY_AWAITING_REPORT  = '_Include_Awaiting_Dr_Summary';


    const   DETAILED_REPORT_HEADER          = ['MESSAGE ID', 'DESTINATION', 'MESSAGE CONTENT', 'ERROR CODE', 'DESCRIPTION CODE', 'RECEIVE_DATETIME', 'SEND DATETIME', 'SENDER',    'USER ID', 'MESSAGE COUNT', 'OPERATOR', 'PRICE'],
            DETAILED_MESSAGE_FORMAT         = ['MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT', 'ERROR_CODE', 'DESCRIPTION_CODE', 'RECEIVE_DATETIME', 'SEND_DATETIME', 'SENDER', 'USER_ID', 'MESSAGE_COUNT', 'OPERATOR', 'PRICE'];



    /**
     * Regular Expression to find a character except GSM 7Bit
     * The messages will set to latin if it only have character are defined on regex
     */
    const   GSM_7BIT_CHARS                  = '~[^A-Za-z0-9 \r\n¤@£$¥èéùìòÇØøÅå\x{0394}_\x{5C}\x{03A6}\x{0393}\x{039B}\x{03A9}\x{03A0}\x{03A8}\x{03A3}\x{0398}\x{039E}ÆæßÉ!\"#$%&\'\(\)*+,\-.\/:;<=>;?¡ÄÖÑÜ§¿äöñüà^{}\[\~\]\|\x{20AC}]~u';


    /**
     * Private properties
     * @var     PDO     $db                 Database connection handler
     */
    public $db,
            $reportDir,
            $finalReportWriter,
            $finalReportReader,
            $awaitingReportWriter,
            $finalReportSummary,
            $awaitingReportSummary,

            $month,
            $year,
            $firstDateOfMonth,
            $lastDateOfMonth,
            $lastFinalStatusDate,

            $today,
            $currentFirstDate,
            $currentYear,
            $currentMonth,
            $currentDay,

            $unchargedDeliveryStatus,
            $periodSuffix
            ;


    /**
     * Public properties
     *
     * @var     Logger  $log                Log handler
     * @var     Array   $queryHistory       History of SQL syntax, total records and execution time
     * @var     Array   $deliveryStatus     Delivery status list for which splitted by CHARGED and UNCHARGED SMS
     */
    public  $log,
            $counter,
            $queryHistory,
            $deliveryStatus,
            $operator;

    /**
     * server timezone
     *
     * @var type String
     */
    public  $timezoneServer = "+0";

    /**
     * server timezone
     *
     * @var type String
     */
    public  $timezoneClient = "+7";

    /**
     * Api Report constructor
     */
    public function __construct($year = null, $month = null, $generateMode = false) {
        $this->log               = Logger::getLogger(get_class($this));

        $this->year              = sprintf('%02d', $year  ?: date('Y'));
        $this->month             = sprintf('%02d', $month ?: date('m'));
        $this->reportDir         = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT.$year.'/'.$month;

        $this->prepareReportDir();
        $this->configureBillingPeriod();

        $this->periodSuffix      = '_'.date('M_Y', strtotime($year.'-'.$month));
        $this->queryHistory      = [];
        $this->counter           = ['charged' => 0, 'uncharged'];

        $this->db                = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
        $this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);


        $this->deliveryStatus    = $this->getDeliveryStatus();
    }




    /**
     *
     * Configure billing period
     * -------------------------------------
     * calculate
     * first date of the month
     * and last date of the month
     * and last send date
     *
     */
    private function configureBillingPeriod() {
        $currentDay         = (int)date('d');
        $currentMonth       = (int)date('m');
        $lastMonth          = (int)date('m', strtotime('last month'));
        $clientDate         = $this->clientTimeZone(strtotime('now'),'Y-m-d 00:00:00');
        $lastTwoDays        = $this->serverTimeZone(strtotime($clientDate.' -2 days'));
        $reportDate         = $this->year.'-'.$this->month.'-01 00:00:00';

        $this->firstDateOfMonth    = $this->serverTimeZone(strtotime($reportDate.' -1 second'));
        $this->lastDateOfMonth     = $this->serverTimeZone(date('Y-m-01 00:00:00', strtotime($reportDate.' +1 month')));

        if($this->month != $currentMonth) {
            if($this->month == $lastMonth && $currentDay < 3) {
                $this->lastFinalStatusDate = $lastTwoDays;
            }
            else {
                $this->lastFinalStatusDate = $this->lastDateOfMonth;
            }
        }
        else {
            if( $currentDay >= 3) {
                $this->lastFinalStatusDate = $lastTwoDays;
            }
            else {
                $this->lastFinalStatusDate = false;
            }
        }
    }




    /**
     * Prepare report directory
     * this function will check report directory and trying to create the directory if not exist
     */
    private function prepareReportDir() {
        if (!@is_dir($this->reportDir)) {
            $this->log->info('Create Report directory "'.$this->reportDir.'"');
            if(!@mkdir($this->reportDir, 0777, TRUE)){
                $this->log->error('Could not create Report directory "'.$this->reportDir.'", please check the permission.');
                $this->log->info ('Cancel generate Report.');
            }
        }

        if(!file_exists(BILLING_QUERY_HISTORY_DIR)){
            if(!@mkdir(BILLING_QUERY_HISTORY_DIR, 0777, true)){
                $this->log->error('Could not create History directory "'.BILLING_QUERY_HISTORY_DIR.'", please check the permission.');
                $this->log->info ('Cancel generate Report.');
            }
        }
    }




    /**
     * Get Current time in second.micro_second
     *
     * @return  Float
     */
    public function getMicroTime() {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }




    /**
     * Store Query information for debug query peformance                       <br />
     * all the query history saved on $this->queryHistory with 2D Array format  <br />
     * [['query', 'totalRecord', 'executionTime']]
     *
     * @param String    $queryCommand       sql command which executed
     * @param Int       $totalRecords       Total of record found
     * @param Float     $startTime          Starting time get from getMicroTime()
     */
    private function storeQueryTime($queryCommand, $totalRecords, $startTime) {
        $executionTime        = ($this->getMicroTime() - $startTime).' sec';
        $query                = preg_replace('/ +/', ' ', $queryCommand);
        $currentMemoryUsed    = (int)(memory_get_usage(1) /1024/1024);
        $executedAt           = date('Y-m-d H:i:s');

        $this->queryHistory[] = compact('query', 'totalRecords', 'executionTime','currentMemoryUsed');
        $fileName = BILLING_QUERY_HISTORY_DIR.'new_billing_peformance.history';

        if ($f = fopen($fileName, file_exists($fileName) ? 'a' : 'w')) {
            fwrite($f, json_encode(compact('query', 'totalRecords', 'executionTime', 'currentMemoryUsed','executedAt'),192).PHP_EOL.'---------------'.PHP_EOL);
            fclose($f);
        }
    }




    /**
     * Query Handler                                                            <br />
     * have 4 different mode 'ALL', 'SINGLE_ROW', 'SINGLE_COLUMN'               <br />
     * and 'SINGLE_COLUMN_AND_ROW'. Each mode could be chose by set the $mode,  <br />
     * A mode was set to make the returned data simple and                      <br />
     * have been formatted as needed to reduce double both                      <br />
     * proccess and validating
     *
     * @param   String  $query      Query String
     * @param   String  $mode
     * @return  Mixed
     */
    private function query(String $query, String $mode = '') {
        try {
            $startTime = $this->getMicroTime();
            $return    = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            $this->storeQueryTime($query, count($return), $startTime);

            switch (strtoupper($mode)) {
                case self::QUERY_SINGLE_ROW :
                    $return = isset($return[0])
                                ? $return[0]
                                : [];
                    break;
                case self::QUERY_SINGLE_COLUMN :
                    $return = array_map(
                                function($record) {
                                    return current($record);
                                },
                                $return
                            );
                    break;
                case self::QUERY_SINGLE_ROW_AND_COLUMN :
                    $return = isset($return[0])
                                ? is_array($return[0])
                                    ? current($return[0])
                                    : $return[0]
                                : false;
                    break;
            }

            return $return;
        }
        catch (Exception $e) {
            $this->log->error('Failed to get data from database.');
            $this->log->debug($e->getMessage());
            $this->log->debug(debug_print_backtrace());
            return [];
        }
        catch (Throwable $e) {
            $this->log->error('Failed to get data from database.');
            $this->log->debug($e->getMessage());
            $this->log->debug(debug_print_backtrace());
            return [];
        }
    }




    /**
     * Function to execute query and return the last inserted ID
     */
    private function exec_query(String $query) {
        try {
            $startTime = $this->getMicroTime();
            $return    = $this->db->query($query);
            return $this->db->lastInsertId();
        }
        catch (Exception $e) {
            $this->log->error('Failed to insert data to database.');
            $this->log->debug($e->getMessage());
            $this->log->debug(debug_print_backtrace());
            return [];
        }
    }




    /**
     * Get all User list from SMS_API_V2.USER
     *
     * @return  Mixed   2D Array [['USER_ID', 'USER_NAME', 'BILLING_PROFILE_ID', 'BILLING_REPORT_GROUP_ID', 'BILLING_TIERING_GROUP_ID']]
     */
    public function getUserDetail($userId = null, $billingProfile = null) {
        $whereClause = !is_null($userId) || !is_null($billingProfile)
                        ? ' WHERE '
                        : '';

        $userClause = !is_null($userId)
                        ? ' USER_ID ' . (
                                is_array($userId)
                                    ? ' IN ('.implode(',', $userId ?: ['\'\'']).')'
                                    : ' = '.$userId)
                        : '';

        $billingClause = !is_null($billingProfile)
                            ? (!is_null($userId) ? ' AND ' : '' ). ' BILLING_PROFILE_ID = '.$billingProfile.' '
                            : '';
        return $this->query(
                 ' SELECT   USER_ID, USER_NAME, BILLING_PROFILE_ID, BILLING_REPORT_GROUP_ID, BILLING_TIERING_GROUP_ID '
                .' FROM     '.DB_SMS_API_V2.'.USER '
                .  $whereClause
                .  $userClause
                .  $billingClause
                .' ORDER BY BILLING_PROFILE_ID'
                , is_null($userId) ?: self::QUERY_SINGLE_ROW
            );
    }


    /**
     * Get billing report list
     *
     * @param int $clientId
     * @return  array
     */
    public function getBillingReport($clientId = null)
    {
        $query = "SELECT USER_NAME FROM USER
                    WHERE BILLING_REPORT_GROUP_ID IS NULL
                    AND USER.BILLING_PROFILE_ID IN (
                        SELECT BILLING_PROFILE.BILLING_PROFILE_ID FROM " . DB_BILL_PRICELIST . ".BILLING_PROFILE
                    )";

        if (!is_null($clientId)) {
            $query .= " AND CLIENT_ID = {$clientId}";
        }

        return $this->db->query($query)->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Get billing report group list
     *
     * @param int $clientId
     * @return  array
     */
    public function getBillingReportGroup($clientId = null)
    {
        $query = "SELECT NAME FROM " . DB_BILL_PRICELIST . ".BILLING_REPORT_GROUP";

        if (!is_null($clientId)) {
            $query .= " WHERE BILLING_REPORT_GROUP.BILLING_REPORT_GROUP_ID IN
                (SELECT USER.BILLING_REPORT_GROUP_ID FROM USER WHERE CLIENT_ID = {$clientId})";
        }

        return $this->db->query($query)->fetchAll(PDO::FETCH_COLUMN, 0);
    }


    /**
     * Function to get all user that in the same billing profile
     *
     * @param Int $userId
     * @return Array
     */
    public function getUserBillingGroup($userId)
    {
        $user = $this->getUserDetail($userId);
        $billingProfileId = $user['BILLING_PROFILE_ID'];

        return !empty($billingProfileId) ? $this->query(
                        ' SELECT   USER_ID, USER_NAME, BILLING_PROFILE_ID'
                        . ' FROM     ' . DB_SMS_API_V2 . '.USER '
                        . ' WHERE BILLING_PROFILE_ID = ' . $billingProfileId
                        . ' ORDER BY USER_ID'
                ) : [];
    }

    /**
     * Get Delivery status from BILL_U_MESSAGE.DELIVERY_STATUS
     *
     * @return  Array   2D Array [['ERROR_CODE', 'STATUS', 'IS_RECREDITED']]
     */
    public function getDeliveryStatus($statusType = 'STATUS_ALL') {
        switch(strtoupper($statusType)) {
            case self::SMS_STATUS_CHARGED   : $statusClause = ' WHERE IS_RECREDITED = 0 '; break;
            case self::SMS_STATUS_UNCHARGED : $statusClause = ' WHERE IS_RECREDITED = 1 '; break;
            default                         : $statusClause = '';
        }
        $status = [];
        $deliveryStatus = $this->query(
                                ' SELECT   DASHBOARD_STATUS, ERROR_CODE, IS_RECREDITED '
                               .' FROM     '.DB_BILL_U_MESSAGE.'.DELIVERY_STATUS '
                               .  $statusClause
                            );

        foreach ($deliveryStatus as &$delivery) {
            $status[$delivery['ERROR_CODE']] = !(bool) $delivery['IS_RECREDITED']
                                                ? $delivery['DASHBOARD_STATUS'] == 'Delivered'
                                                    ? self::SMS_STATUS_DELIVERED
                                                    : self::SMS_STATUS_UNDELIVERED_CHARGED
                                                : self::SMS_STATUS_UNDELIVERED;
        }

        return $status;
    }




    /**
     * Get Detail of Billing Profile detail                                     <br />
     * would not return the rule of billing report                              <br />
     * use getOperatorBaseDetail() or getTieringDetail() instead to get price rule
     *
     * @return  Array   Array ['BILLING_TYPE', 'DESCRIPTION', 'CREATED_AT', 'UPDATED_AT']
     */
    public function getBilingProfileDetail($billingProfileId = null) {
        $profileIdClause = !is_null($billingProfileId)
                                ? ' WHERE    BILLING_PROFILE_ID = '.$billingProfileId
                                : '';
        return  $this->query(
                         ' SELECT   BILLING_PROFILE_ID, NAME, BILLING_TYPE, DESCRIPTION, CREATED_AT, UPDATED_AT'
                        .' FROM     '.DB_BILL_PRICELIST.'.BILLING_PROFILE'
                        .  $profileIdClause
                        ,  is_null($billingProfileId) ?: self::QUERY_SINGLE_ROW
                    );
    }




    /**
     * Get Operator Base Price rule
     *
     * @return  Array   2D Array [['OP_ID', 'PER_SMS_PRICE']]
     */
    public function getOperatorBaseDetail($billingProfileId) {
        return $this->query(
                         ' SELECT   OP_ID, PER_SMS_PRICE'
                        .' FROM     '.DB_BILL_PRICELIST.'.BILLING_PROFILE_OPERATOR'
                        .' WHERE    BILLING_PROFILE_ID = '.$billingProfileId
                    );
    }




    /**
     * Get operator detail by given operator id
     *
     * @param   Array   $opId   List of operator id
     * @return  Mixed           List of Operator details or null
     */
    public function getOperatorDetail(Array $opId = [] ) {
        $opClause = !empty($opId)
                        ? ' WHERE    OP_ID IN (\''.implode('\',\'', $opId).'\') '
                        : '' ;

        return $this->query(
                        ' SELECT   OP_COUNTRY_CODE, OP_ID '
                       .' FROM     '.DB_First_Intermedia.'.OPERATOR '
                       .  $opClause
                   );
    }




    /**
     * Get Operator Dial Prefix form First_Intermedia.OPERATOR_DIAL_PREFIX      <br />
     *
     * @return  Array   2D Array [['OP_ID', 'RANGE_LOWER', 'RANGE_UPPER']]
     */
    public function getOperatorDialPrefix(Array $opId = []){
        $opClause = !empty($opId)
                        ? ' WHERE OP_ID IN(\''.implode('\',\'', $opId).'\') '
                        : '' ;
        return $this->query(
                         ' SELECT   OP_ID,'
                         .'OP_DIAL_RANGE_LOWER as RANGE_LOWER,'
                         .'OP_DIAL_RANGE_UPPER as RANGE_UPPER '
                        .' FROM     '.DB_First_Intermedia.'.OPERATOR_DIAL_PREFIX '
                        .  $opClause
                        .' ORDER BY OP_ID '
                    );
    }




    /**
     * Get Tiering Base Price Rule
     *
     * @return  Array   2D Array [['SMS_COUNT_FROM', 'SMS_COUNT_UP_TO', 'PER_SMS_PRICE']]
     */
    public function getTieringDetail($billingProfileId) {
        return !empty($billingProfileId) ? $this->query(
                         ' SELECT   SMS_COUNT_FROM, SMS_COUNT_UP_TO, PER_SMS_PRICE'
                        .' FROM     '.DB_BILL_PRICELIST.'.BILLING_PROFILE_TIERING'
                        .' WHERE    BILLING_PROFILE_ID = '.$billingProfileId
                    )
                : [];
    }




    /**
     * Get Tiering Group Detail                                                 <br />
     * Would not return accumulate SMS_TRAFFIC or User List                     <br />
     * use getTieringGroupUserList() instead to get the User List
     *
     * @return  Mixed       List or an Array                                    <br />
     *                      ['NAME', 'DESCRIPTION', 'CREATED_AT', 'UPDATED_AT']
     */
    public function getTieringGroupDetail($tieringGroupId = null) {
        $groupClause = !is_null($tieringGroupId)
                            ? ' WHERE    BILLING_TIERING_GROUP_ID = '.$tieringGroupId
                            : '';

        return $this->query(
                         ' SELECT   BILLING_TIERING_GROUP_ID , NAME, DESCRIPTION, CREATED_AT, UPDATED_AT'
                        .' FROM     '.DB_BILL_PRICELIST.'.BILLING_TIERING_GROUP'
                        .  $groupClause
                        ,  is_null($tieringGroupId) ?: self::QUERY_SINGLE_ROW
                    );
    }




    /**
     * Get Tiering Group User List which accumulate the same Tiering Rule
     *
     * @return  Array   Array ['USER_ID','USER_NAME']
     */
    public function getTieringGroupUserList($tieringGroupId) {
        return !empty($tieringGroupId) ? $this->query(
                         ' SELECT   USER_ID, USER_NAME'
                        .' FROM     '.DB_SMS_API_V2.'.USER'
                        .' WHERE    BILLING_TIERING_GROUP_ID = '.$tieringGroupId
                    )
                : [];
    }




    /**
     * Get user or group tiering monthly traffic
     *
     * @param   Mixed   $userIds        User Id or list of user id if need group traffic
     * @param   Bool    $awaitingDr     Accumulate traffic including sms awaiting dr
     * @return  Int
     */
    public function getTieringTraffic($userIds, $awaitingDr = false) {
        $userIds      = is_array($userIds)
                            ? array_column($userIds, 'USER_ID')
                            : $userIds;

        $usersClause  = is_array($userIds)
                            ? ' IN ('.implode(',', $userIds).') '
                            : ' = '.$userIds;

        $endDate      = $awaitingDr
                            ? $this->lastDateOfMonth
                            : $this->lastFinalStatusDate;

        return $this->query(
                        ' SELECT   COUNT(USER_ID_NUMBER) '
                        . ' FROM  (SELECT '
                                    . 'MESSAGE_STATUS,'
                                    . 'STR_TO_DATE(SUBSTRING(MESSAGE_ID, 5, 19), \'%Y-%m-%d %H:%i:%s\') AS RECEIVE_DATETIME,'
                                    . 'USER_ID_NUMBER '
                        . ' FROM '. DB_SMS_API_V2 . '.USER_MESSAGE_STATUS) USER_MESSAGE_STATUS '
                        . ' WHERE    USER_MESSAGE_STATUS.USER_ID_NUMBER ' . $usersClause
                        . '          AND USER_MESSAGE_STATUS.RECEIVE_DATETIME > \'' . $this->firstDateOfMonth . '\''
                        . '          AND USER_MESSAGE_STATUS.RECEIVE_DATETIME < \'' . $endDate . '\''
                        . '          AND USER_MESSAGE_STATUS.MESSAGE_STATUS NOT IN (\'' . $this->unchargedDeliveryStatus . '\') '
                        , self::QUERY_SINGLE_ROW_AND_COLUMN
                ) ?: 0;
    }




    /**
     * Get apllied price from pricing list by given traffic
     *
     * @param   Array   $rules      pricing list
     * @param   Int     $traffic    Total traffic
     * @return  Array               applied Price
     */
    private function getTieringPriceByTraffic(&$rules, $traffic) {
        foreach($rules as &$rule) {
            $min = is_numeric($rule['SMS_COUNT_FROM'])  ? (int)$rule['SMS_COUNT_FROM']  : 0;
            $max = is_numeric($rule['SMS_COUNT_UP_TO']) ? (int)$rule['SMS_COUNT_UP_TO'] : PHP_INT_MAX;
            if(($min <= $traffic) && ($traffic <= $max)) {
                return [$rule];
            }
        }
        return [['PER_SMS_PRICE' => 0]];
    }




    /**
     * Get one or all Report Group detail                                       <br />
     * Would not return User list who's merge their report file                 <br />
     * use getReportGroupUserList() instead to get the User List                <br />
     *
     * @param   Int     $reportGroupId  Billing Report Group Id
     * @return  Mixed   List or an array of report group detail                 <br />
     *                  ['NAME', 'DESCRIPTION', 'CREATED_AT', 'UPDATED_AT']
     */
    public function getReportGroupDetail($reportGroupId = null) {
        $groupClause = !is_null($reportGroupId)
                            ? ' WHERE    BILLING_REPORT_GROUP_ID = '.$reportGroupId
                            : '';

        return $this->query(
                         ' SELECT   BILLING_REPORT_GROUP_ID, NAME, DESCRIPTION, CREATED_AT, UPDATED_AT'
                        .' FROM     '.DB_BILL_PRICELIST.'.BILLING_REPORT_GROUP'
                        .  $groupClause
                        ,  is_null($reportGroupId) ?: self::QUERY_SINGLE_ROW
                    );
    }




    /**
     * Get Report Group User List who's join their report files
     *
     * @return  Array   Array ['USER_ID','USER_NAME']
     */
    public function getReportGroupUserList($reportGroupId) {
        return $this->query(
                         ' SELECT   USER_ID, USER_NAME'
                        .' FROM     '.DB_SMS_API_V2.'.USER'
                        .' WHERE    BILLING_REPORT_GROUP_ID = '.$reportGroupId
                    );
    }




    /**
     * Get last message send date time for group of user
     * @param   Array   $userIds    list of user id
     * @return  Array               list of user last message send date time
     */
    public function getGroupLastSendDate($userIds)
    {
        $dates = [];
        foreach ($userIds as $userId) {
            $lastSendDate = $this->query(
                    '   SELECT USER_MESSAGE_STATUS.RECEIVE_DATETIME '
                    . ' FROM (SELECT '
                            . 'USER_ID_NUMBER,'
                            . 'STR_TO_DATE(SUBSTRING(MESSAGE_ID, 5, 19), \'%Y-%m-%d %H:%i:%s\') AS RECEIVE_DATETIME'
                    . ' FROM '. DB_SMS_API_V2 .'.USER_MESSAGE_STATUS) USER_MESSAGE_STATUS '
                    . ' WHERE  USER_MESSAGE_STATUS.USER_ID_NUMBER = ' . $userId
                            . ' AND USER_MESSAGE_STATUS.RECEIVE_DATETIME > \'' . $this->firstDateOfMonth . '\''
                            . ' AND USER_MESSAGE_STATUS.RECEIVE_DATETIME < \'' . $this->lastFinalStatusDate . '\''
                    . ' ORDER BY USER_MESSAGE_STATUS.RECEIVE_DATETIME DESC '
                    . ' LIMIT    1'
                    , self::QUERY_SINGLE_ROW_AND_COLUMN
            );

            $dates[$userId] = $lastSendDate == false
                                ? $this->firstDateOfMonth
                                : $lastSendDate;
        }

        return $dates;
    }

    /**
     * Get User Message list from SMS_API_V2.USER_MESSAGE_STATUS                                            <br />
     * could get by one or several USER_ID (TIERING_GROUP or REPORT_GROUP)                                  <br />
     *
     * @param   Mixed       $userId                 a string for single USER_ID                             <br />
     *                                              and an Array of USER_ID for mutiple USER_ID
     * @param   String      $startDateTime          the begining of SEND_DATETIME
     * @param   String      $endDateTime            the end of SEND_DATETIME
     * @param   Int         $dataSize               limit of record on one query
     * @param   Int         $startIndex             number of skipped row to return
     * @return  Array                               2D Array [[                                                 <br />
     *                                                  'MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT',         <br />
     *                                                  'MESSAGE_STATUS', 'DESCRIPTION_CODE', 'SEND_DATETIME',  <br />
     *                                                  'SENDER', 'USER_ID', 'MESSAGE_COUNT'                    <br />
     *                                              ]]
     */
    public function getUserMessageStatus($userId, $startDateTime, $endDateTime, $dataSize, $startIndex) {
        //MESSAGE ID                    DESTINATION	MESSAGE CONTENT	ERROR CODE	DESCRIPTION CODE	SEND DATETIME           SENDER      USER ID     MESSAGE COUNT	||  OPERATOR    PRICE
        //5GPI2017-04-11 06:13:29.470	15629689999	Test msg sms	0+0+0+0         DELIVERED               2017-04-09 00:00:00	1rstWAP     PEPTrial                1	||  DEFAULT     315
        $userIdClause = is_array($userId)
                            ? ' IN ('.implode(',', $userId).')'
                            : ' = '.$userId;

        $message  =  ' SELECT USER_MESSAGE_STATUS.MESSAGE_ID,'
                            . 'USER_MESSAGE_STATUS.DESTINATION,'
                            . 'USER_MESSAGE_STATUS.MESSAGE_CONTENT,'
                            . 'USER_MESSAGE_STATUS.MESSAGE_STATUS,'
                            . '\'\' AS DESCRIPTION_CODE,'
                            . 'USER_MESSAGE_STATUS.RECEIVE_DATETIME,'
                            . 'USER_MESSAGE_STATUS.SEND_DATETIME,'
                            . 'USER_MESSAGE_STATUS.SENDER,'
                            . 'USER_MESSAGE_STATUS.USER_ID'
                    . ' FROM  (SELECT '
                            . 'MESSAGE_ID,'
                            . 'DESTINATION,'
                            . 'MESSAGE_CONTENT,'
                            . 'MESSAGE_STATUS,'
                            . 'SEND_DATETIME,'
                            . 'STR_TO_DATE(SUBSTRING(MESSAGE_ID, 5, 19), \'%Y-%m-%d %H:%i:%s\') AS RECEIVE_DATETIME,'
                            . 'SENDER,'
                            . 'USER_ID_NUMBER,'
                            . 'USER_ID'
                    . ' FROM '. DB_SMS_API_V2 . '.USER_MESSAGE_STATUS) USER_MESSAGE_STATUS '
                    . ' WHERE     USER_MESSAGE_STATUS.USER_ID_NUMBER '.$userIdClause
                    . '           AND USER_MESSAGE_STATUS.RECEIVE_DATETIME >  \''.$startDateTime.'\' '
                    . '           AND USER_MESSAGE_STATUS.RECEIVE_DATETIME < \''.$endDateTime  .'\' '
                    . ' ORDER BY USER_MESSAGE_STATUS.MESSAGE_ID ASC '
                    . ' LIMIT     '.$startIndex.','.$dataSize;
        $messages = $this->query($message);
        $this->log->debug("result ".$message);

        return $messages;
    }




    /**
     * Get Message status by group
     *
     * @param   Array     $users            List of user with their last message send date time [['USER_ID','SEND_DATETIME']]
     * @param   String    $endDateTime      End Time
     * @param   String    $dataSize         Limit per query index
     * @param   String    $startIndex       Start index
     * @return  Array                       2D Array [[                                                     <br />
     *                                              'MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT',         <br />
     *                                              'MESSAGE_STATUS', 'DESCRIPTION_CODE', 'SEND_DATETIME',  <br />
     *                                              'SENDER', 'USER_ID', 'MESSAGE_COUNT'                    <br />
     *                                      ]]
     */
    public function getGroupMessageStatus(array $users, $endDateTime, $dataSize, $startIndex) {
        $userClause  = [];
        foreach($users as $userId => $startDateTime) {
            $userClause[] = '( USER_MESSAGE_STATUS.USER_ID_NUMBER = '.$userId
                           .'  AND USER_MESSAGE_STATUS.RECEIVE_DATETIME > \''.$startDateTime.'\''
                           .'  AND USER_MESSAGE_STATUS.RECEIVE_DATETIME < \''.$endDateTime.'\')';
        }

        if(empty($userClause)){
            return [];
        }

        $messages = $this->query(
                       ' SELECT USER_MESSAGE_STATUS.MESSAGE_ID,'
                            . 'USER_MESSAGE_STATUS.DESTINATION,'
                            . 'USER_MESSAGE_STATUS.MESSAGE_CONTENT,'
                            . 'USER_MESSAGE_STATUS.MESSAGE_STATUS,'
                            . '\'\' AS DESCRIPTION_CODE,'
                            . 'USER_MESSAGE_STATUS.RECEIVE_DATETIME,'
                            . 'USER_MESSAGE_STATUS.SEND_DATETIME,'
                            . 'USER_MESSAGE_STATUS.SENDER,'
                            . 'USER_MESSAGE_STATUS.USER_ID'
                    . ' FROM  (SELECT '
                            . 'MESSAGE_ID,'
                            . 'DESTINATION,'
                            . 'MESSAGE_CONTENT,'
                            . 'MESSAGE_STATUS,'
                            . 'SEND_DATETIME,'
                            . 'STR_TO_DATE(SUBSTRING(MESSAGE_ID, 5, 19), \'%Y-%m-%d %H:%i:%s\') AS RECEIVE_DATETIME,'
                            . 'SENDER,'
                            . 'USER_ID_NUMBER,'
                            . 'USER_ID'
                    . ' FROM '. DB_SMS_API_V2 . '.USER_MESSAGE_STATUS) USER_MESSAGE_STATUS '
                       .' WHERE     ('.implode(' OR ', $userClause).')'
                       .' ORDER BY USER_MESSAGE_STATUS.MESSAGE_ID ASC '
                       .' LIMIT     '.$startIndex.','.$dataSize
                    );
        $this->log->info('query getGroupMessageStatus '.json_encode($userClause));

        return $messages;
    }



    /**
     * Calculate message base on the message type
     *
     * @param   String  $message        Message content
     * @return  Int                     Message Count
     */
    private function getMessageCount($message) {
        $messageLength = mb_strlen($message);

        if($this->isGsm7bit($message)){
            return  $messageLength <= self::GSM_7BIT_SINGLE_SMS
                        ? 1
                        : ceil( $messageLength / self::GSM_7BIT_MULTIPLE_SMS );
        }else{
            return  $messageLength <= self::UNICODE_SINGLE_SMS
                        ? 1
                        : ceil( $messageLength / self::UNICODE_MULTIPLE_SMS );
        }
    }




    /**
     * Get Message Status Description by given ERROR_CODE
     *
     * @param   String  $errorCode      Error_Code | Message_Status
     * @return  String                  Description of ERROR_CODE
     */
    private function getMessageStatus(&$message) {
        $errorCode = $message['MESSAGE_STATUS'];

        return isset($this->deliveryStatus[$errorCode])
                ? $this->deliveryStatus[$errorCode]
                : self::SMS_STATUS_UNDEFINED;
    }




    /**
     * Check if the message was Gsm_7bit or Unicode encoded
     *
     * @param   String  $message        Message content
     * @return  boolean                 true for Gsm7bit and false for unicode
     */
    private function isGsm7bit($message) {
        return preg_match(self::GSM_7BIT_CHARS, $message) === 0;
    }




    /**
     * Parsing the Destination number to get it's own Operator Name
     *
     * @param   String  $destination    Destination number wich will be parsing
     * @param   Array   $operators      2D Array of Operator                                <br />
     *                                  could be get from getOperatorDialPrefix()           <br />
     *                                  [['OP_ID', 'RANGE_LOWER', 'RANGE_UPPER']]           <br />
     * @return  String                  Operator Name or self::DEFAULT_OPERATOR
     */
    private function getDestinationOperator($destination, &$operators) {
        foreach($operators as &$operator) {
            if( $operator['OP_ID'] !== self::DEFAULT_OPERATOR
                && !empty($operator['RANGE_LOWER'])
                && !empty($operator['RANGE_UPPER'])
                && $destination >= $operator['RANGE_LOWER']
                && $destination <= $operator['RANGE_UPPER']
            )
            {
                return $operator['OP_ID'];
            }
        }
        return self::DEFAULT_OPERATOR;
    }




    /**
     * Assign message price base on Billing profile type
     *
     *
     * @param   String  $type       Billing Type, defined on ApiReport::BILLING_*_BASE
     * @param   Array   $messages   2D Array [[                                                 <br />
     *                                  'MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT',         <br />
     *                                  'MESSAGE_STATUS', 'DESCRIPTION_CODE', 'SEND_DATETIME',  <br />
     *                                  'SENDER', 'USER_ID', 'MESSAGE_COUNT'                    <br />
     *                              ]]
     * @param   Array   $rule       2D array of Pricing  [['OP_ID', 'PER_SMS_PRICE']]
     * @param   Array   $operator   2D Array of Billing Rule                                    <br />
     *                              [['OP_ID', 'PREFIX', 'MIN_LENGTH', 'MAX_LENGTH']]
     * @return  Int                 Total price
     */
    public function assignMessagePrice(String $type, Array &$messages, Array &$rules, Array &$operators = []) {
        return $type == self::BILLING_OPERATOR_BASE
                    ? $this->assignOperatorPrice($messages, $rules, $operators)
                    : $this->assignTieringPrice ($messages, $rules, $operators);
    }




    /**
     * Assign message price when billing profil was in TIERING or FIXED price Base
     *
     * @param Array     $messages   2D Array [[                                                 <br />
     *                                  'MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT',         <br />
     *                                  'MESSAGE_STATUS', 'DESCRIPTION_CODE', 'SEND_DATETIME',  <br />
     *                                  'SENDER', 'USER_ID', 'MESSAGE_COUNT'                    <br />
     *                              ]]
     * @param   Array   $rule       2D array of Pricing  [['OP_ID', 'PER_SMS_PRICE']]
     */
    private function assignTieringPrice(&$messages, &$rules, &$operators) {
        $price         =  current($rules)['PER_SMS_PRICE'];

        foreach($messages as &$message) {
            /**
             * Validate if the message already formated or not
             */
            if(empty($message['DESCRIPTION_CODE'])){
                $this->formatMessageData($message, $operators);
            }
            $message['PRICE']    = $message['DESCRIPTION_CODE'] !== self::SMS_STATUS_UNDELIVERED
                                    ? ($price *  $message['MESSAGE_COUNT'])
                                    : 0;
        }
    }


    /**
     * Assign message price when billing profil is Operator Base
     *
     * @param   Array   $messages   2D Array [[                                                 <br />
     *                                  'MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT',         <br />
     *                                  'MESSAGE_STATUS', 'DESCRIPTION_CODE', 'SEND_DATETIME',  <br />
     *                                  'SENDER', 'USER_ID', 'MESSAGE_COUNT'                    <br />
     *                              ]]
     * @param   Array   $rule       2D array of Pricing  [['OP_ID', 'PER_SMS_PRICE']]
     * @param   Array   $operator   2D Array of Billing Rule                                    <br />
     *                              [['OP_ID', 'RANGE_LOWER', 'RANGE_UPPER']]
     */
    private function assignOperatorPrice(&$messages, &$rules, &$operators) {
        $chargedStatus = [self::SMS_STATUS_DELIVERED, self::SMS_STATUS_UNDELIVERED_CHARGED];
        $mappedRules = array_column($rules, 'PER_SMS_PRICE', 'OP_ID');

        foreach($messages as &$message) {
            $this->formatMessageData($message, $operators);

            if($message['DESCRIPTION_CODE'] !== self::SMS_STATUS_UNDELIVERED){
                $price              = $mappedRules[$message['OPERATOR']];
                $message['PRICE']   = $price *  $message['MESSAGE_COUNT'];
            } else {
                $message['PRICE'] = 0;
            }
        }
    }


    /**
     * Format SEND_DATETIME, RECEIVE_DATETIME, DESCRIPTION CODE, MESSAGE_COUNT, OPERATOR field
     *
     * @param $message array containing details about one specific message, such as the content, send datetime, etc.
     * @param $operators array containing operator data
     * @return void
     */
    private function formatMessageData(&$message, &$operators){
        $message['SEND_DATETIME']    = $this->clientTimeZone($message['SEND_DATETIME']);
        $message['RECEIVE_DATETIME'] = $this->clientTimeZone($message['RECEIVE_DATETIME']);
        $message['DESCRIPTION_CODE'] = $this->getMessageStatus($message);
        $message['MESSAGE_COUNT']    = $this->getMessageCount($message['MESSAGE_CONTENT']);
        $message['OPERATOR']         = $this->getDestinationOperator($message['DESTINATION'], $operators);
    }


    /**
     * Load last message send date time for every user
     *
     * @param   String  $dir        directory path for specific report period
     * @return  Mixed               List or string of User Last Message send date
     */
    public function loadLastMessageDate($userId = null) {
        $cache = $this->loadCache(self::CACHE_LAST_DATE);
        if(!is_null($userId)) {
            foreach($cache as $id => $date) {
                if($id == $userId) {
                    return $date;
                }
            }
            return $this->firstDateOfMonth;
        }
        return $cache;
    }




    /**
     * Save last message send date time for every user
     *
     * @param   Array   $data       List of all user messages last send date time
     *                              [['USER_ID', 'RECEIVE_DATETIME']]
     * @param   String  $dir        directory path for specific report period
     * @return  Bool                Save status
     */
    private function saveLastMessageDate(Array &$data) {
        $cache  = $this->mergeArray($this->loadLastMessageDate(), $data);
        return $this->saveCache(self::CACHE_LAST_DATE, $cache);
    }




    /**
     * Load single or all Billing Profile detail from cache
     *
     * @param   Int     $profileId      Billing Profile id
     * @return  Mixed                   Billing profile Detail or null
     */
    public function loadBillingProfileCache($profileId = null) {
        $cache = $this->loadCache(self::CACHE_BILLING_PROFILE);

        if(empty($cache)) {
            $cache = $this->getBilingProfileDetail();
            foreach($cache as &$profile) {
                if($profile['BILLING_TYPE'] == self::BILLING_TIERING_BASE) {
                    $profile['PRICING'] = $this->getTieringDetail($profile['BILLING_PROFILE_ID']);
                }
                else {
                    $profile['PRICING'] = $this->getOperatorBaseDetail($profile['BILLING_PROFILE_ID']);
                    $profile['PREFIX']  = $this->getOperatorDialPrefix(array_column($profile['PRICING'], 'OP_ID'));
                }
            }

            $this->saveCache(self::CACHE_BILLING_PROFILE, $cache);
        }

        if(!is_null($profileId)) {
            $key   = array_search(
                        $profileId,
                        array_column(
                            $cache,
                            'BILLING_PROFILE_ID'
                        )
                    );
            if($key !== false){
                $cache = $cache[$key];
            }else{
                $newCache = $this->getBilingProfileDetail($profileId);
                if(!empty($newCache)){

                    if($newCache['BILLING_TYPE'] == self::BILLING_TIERING_BASE) {
                        $newCache['PRICING'] = $this->getTieringDetail($profileId);
                    } else {
                        $newCache['PRICING'] = $this->getOperatorBaseDetail($profileId);
                        $newCache['PREFIX']  = $this->getOperatorDialPrefix(array_column($newCache['PRICING'], 'OP_ID'));
                    }

                    $cache[] = $newCache;
                    $this->saveCache(self::CACHE_BILLING_PROFILE, $cache);
                    $cache = $newCache;
                }else{
                    $cache = null;
                }
            }
        }

        return $cache;
    }




    /**
     * Load single or all Report group detail from cache
     *
     * @param   Int     $groupId    Group Id
     * @return  Mixed               Report group detail or null
     */
    public function loadReportGroupCache($groupId = null) {
        $cache = $this->loadCache(self::CACHE_REPORT_GROUP);
        if(empty($cache)) {
            $cache  = $this->getReportGroupDetail();

            foreach($cache as &$group) {
                $group['USERS'] = $this->getReportGroupUserList($group['BILLING_REPORT_GROUP_ID']);
            }

            $this->saveCache(self::CACHE_REPORT_GROUP, $cache);
        }

        if(!is_null($groupId)) {
            $key   = array_search(
                        $groupId,
                        array_column(
                            $cache,
                            'BILLING_REPORT_GROUP_ID'
                        )
                    );
            $cache = $key !== false
                        ? $cache[$key]
                        : null;
        }

        return $cache;
    }




    /**
     * Update old cache with new data
     *
     * @param   Array   $data       Old cache Data
     * @param   Array   $newData    New Cache Data
     * @return  Array               Updated cache data
     */
    private function mergeArray(Array $data, Array &$newData) {
        foreach($newData as $key => $val) {
            $data[$key] = $val;
        }
        return $data;
    }




    /**
     * Load cahce file
     * @param type $filName
     * @return type
     */
    private function loadCache($cacheName) {
        $fileName = $this->reportDir.'/cache/'.$cacheName;
        return file_exists($fileName)
                ? json_decode(file_get_contents($fileName), JSON_OBJECT_AS_ARRAY) ?: []
                : [];
    }




    /**
     * Save cache file,
     * this function will automatically try to create cache folder if exist
     *
     * @param   Mixed   $contents   List of new cache
     * @return  Bool                Save status
     */
    private function saveCache($cacheName, &$contents) {
        $fileName = $this->reportDir.'/cache/'.$cacheName;
        try {
            if(!is_dir(dirname($fileName)) && !@mkdir(dirname($fileName), 0777, true) ) {
                throw new Exception('Failed create cache, could not create directory "'. dirname($fileName).'"');
            }
            return file_put_contents($fileName, json_encode($contents));
        }
        catch (Exception $e) {
            $this->log->error('Failed to save last message date, please check permission and storage.');
            $this->log->debug($e->getMessage());
            return false;
        }
        catch (Throwable $e) {
            $this->log->error('Failed to save last message date, please check permission and storage.');
            $this->log->debug($e->getMessage());
            return false;
        }
    }




    /**
     * Create Box\Spout file handler both final and awaiting report
     *
     * @param String    $fileName           Report File name
     * @param Boolean   $isNewFile          Reference to know the report is new file or not
     * @param Array     $newFixedPrice      New Fix price rule
     */
    private function createReportFile($fileName, &$isNewFile, $newFixedPrice = null) {
        $dirFinal              = $this->reportDir.'/'. self::DIR_FINAL_REPORT;
        $dirAwaiting           = $this->reportDir.'/'. self::DIR_AWAITING_REPORT;
        $finalReport           = $dirFinal.       '/'.$fileName.$this->periodSuffix.self::SUFFIX_FINAL_REPORT.'.xlsx';
        $summaryFinalReport    = $dirFinal.       '/'.$fileName.$this->periodSuffix.self::SUFFIX_SUMMARY_FINAL_REPORT.'.xlsx';
        $awaitingReport        = $dirAwaiting.    '/'.$fileName.$this->periodSuffix.self::SUFFIX_AWAITING_REPORT.'.xlsx';
        $summaryAwaitingReport = $dirAwaiting.    '/'.$fileName.$this->periodSuffix.self::SUFFIX_SUMMARY_AWAITING_REPORT.'.xlsx';

        is_dir($dirFinal)    ?: @mkdir($dirFinal, 0777, true);
        is_dir($dirAwaiting) ?: @mkdir($dirAwaiting, 0777, true);


        $this->finalReportSummary    = ['senderId' => [], 'operator' => [], 'userId' => []];
        $this->awaitingReportSummary = ['senderId' => [], 'operator' => [], 'userId' => []];
        //$this->finalReportReader->close();
        $this->finalReportWriter     = WriterFactory::create(Type::XLSX);
        $this->awaitingReportWriter  = WriterFactory::create(Type::XLSX);

        if(file_exists($finalReport) && filesize($finalReport) > 0) {
            $this->log->info('Copy data from existing '.$fileName.' report');
            $this->copyFinalStatusReport($fileName, $newFixedPrice);
            $isNewFile = false;
        }
        else {
            $this->finalReportWriter->openToFile($finalReport);
            $this->finalReportWriter->addRow(self::DETAILED_REPORT_HEADER);

            $this->awaitingReportWriter->openToFile($awaitingReport);
            $this->awaitingReportWriter->addRow(self::DETAILED_REPORT_HEADER);

            $isNewFile = true;
        }
    }




    /**
     * Copy final status message from old report to new report
     *
     * currently Box\Spout does not support to append data into existing file
     * then we implement solution from their guide,
     *
     * this function may be the longest time consumer, since it have to
     * copy alot of data from old file to new file.
     *
     *
     * @param String    $fileName           Report File name
     * @param Array     $newFixedPrice      New Fix Price rule
     */
    private function copyFinalStatusReport($fileName, $newFixedPrice = null) {
        $dirFinal              = $this->reportDir.'/'. self::DIR_FINAL_REPORT;
        $dirAwaiting           = $this->reportDir.'/'. self::DIR_AWAITING_REPORT;
        $oldFinalReport        = $dirFinal.       '/'.$fileName.$this->periodSuffix.self::SUFFIX_FINAL_REPORT.'.xlsx.old';
        $newFinalReport        = $dirFinal.       '/'.$fileName.$this->periodSuffix.self::SUFFIX_FINAL_REPORT.'.xlsx';
        $summaryFinalReport    = $dirFinal.       '/'.$fileName.$this->periodSuffix.self::SUFFIX_SUMMARY_FINAL_REPORT.'.xlsx';
        $awaitingReport        = $dirAwaiting.    '/'.$fileName.$this->periodSuffix.self::SUFFIX_AWAITING_REPORT.'.xlsx';

        $this->log->info('Start Copy '.$fileName.' report file');
        $startTime = $this->getMicroTime();
        if(@rename($newFinalReport, $oldFinalReport)) {
            $oldReportReader = ReaderFactory::create(Type::XLSX);
            $oldReportReader->open($oldFinalReport);

            $this->finalReportWriter->openToFile($newFinalReport);
            $this->awaitingReportWriter->openToFile($awaitingReport);

            foreach ($oldReportReader->getSheetIterator() as $sheetIndex => $sheet) {
                if ($sheetIndex !== 1) {
                    $this->finalReportWriter   ->addNewSheetAndMakeItCurrent();
                    $this->awaitingReportWriter->addNewSheetAndMakeItCurrent();
                }

                foreach ($sheet->getRowIterator() as $row) {
                    if(!empty($row) && current($row) != 'MESSAGE ID') {
                        $fRow = array_combine(self::DETAILED_MESSAGE_FORMAT, $row) ?: [];
                        $aRow = $fRow;

                        if(!empty($fRow)) {
                            if(!is_null($newFixedPrice)) {
                                $fRow['PRICE'] = $fRow['PRICE'] != 0 ? current($newFixedPrice['finalPrice'])   ['PER_SMS_PRICE'] * $fRow['MESSAGE_COUNT'] : 0;
                                $aRow['PRICE'] = $aRow['PRICE'] != 0 ? current($newFixedPrice['awaitingPrice'])['PER_SMS_PRICE'] * $aRow['MESSAGE_COUNT'] : 0;
                                $this->getMessageSummary($fRow, 'final');
                                $this->getMessageSummary($aRow, 'awaiting');
                            }
                            else {
                                $this->getMessageSummary($fRow);
                            }
                        }

                        $this->finalReportWriter   ->addRow($fRow);
                        $this->awaitingReportWriter->addRow($aRow);
                    }
                    else {
                        $this->finalReportWriter   ->addRow($row);
                        $this->awaitingReportWriter->addRow($row);
                    }

                }
            }

            $oldReportReader->close();
            unlink($oldFinalReport);
            unset($oldReportReader);
        }
        else {
            $this->log->warn('Could not rename Old Report file '.$oldFinalReport.' permission denied.');
            $this->log->info('Replace existing file.');
            $this->finalReportWriter->openToFile($newFinalReport);
            $this->awaitingReportWriter->openToFile($awaitingReport);
        }
        $this->log->info('Completed copy '.$fileName.' report file in '.  number_format($this->getMicroTime() - $startTime, 2).' s');
    }




    /**
     * Get Message Summary
     *
     * @param Array     $messages   Array of messages
     * @param String    $type       Insert message detail into ['all', 'final', 'awaiting'] summary
     */
    private function getMessageSummary(&$messages, $type = 'all') {
        $final    = strtolower($type) == 'all' || strtolower($type) == 'final';
        $awaiting = strtolower($type) == 'all' || strtolower($type) == 'awaiting';

        foreach(is_array(current($messages)) ? $messages : [$messages] as $message) {
            if(empty($message)) continue;

            $senderId = $message['SENDER'];
            $sendDate = date('Y-m-d', strtotime($message['RECEIVE_DATETIME']));
            $userName = $message['USER_ID'];
            $status   = $message['DESCRIPTION_CODE'];
            $price    = $message['PRICE'];
            $operator = $message['OPERATOR'];
            $smsCount = $message['MESSAGE_COUNT'];

            if($final) {
                $this->storeSummary($this->finalReportSummary, 'senderId', $senderId, $sendDate, $status, $price, $smsCount);
                $this->storeSummary($this->finalReportSummary, 'operator', $operator, $sendDate, $status, $price, $smsCount);
                $this->storeSummary($this->finalReportSummary, 'userId',   $userName, $sendDate, $status, $price, $smsCount);
            }

            if($awaiting) {
                $this->storeSummary($this->awaitingReportSummary, 'senderId', $senderId, $sendDate, $status, $price, $smsCount);
                $this->storeSummary($this->awaitingReportSummary, 'operator', $operator, $sendDate, $status, $price, $smsCount);
                $this->storeSummary($this->awaitingReportSummary, 'userId',   $userName, $sendDate, $status, $price, $smsCount);
            }
        }
    }




    /**
     * Gererate date range
     * to initialize data from first til end date of the month
     * if new member was added into summary group
     *
     * @param   String    $first            Start Date
     * @param   String    $last             End Date
     * @return  Array                       List of Range date
     */
    public function getDateRange($first, $last) {
        $dates   = [];
        $current = strtotime($first);
        $last    = strtotime($last);

        while( $current <= $last ) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }
        return $dates;
    }




    /**
     * Save message infromation into summary
     *
     * @param Array     $summary    "awaitingReportSummary" or "finalReportSummary"
     * @param String    $group      Group name like 'operator' or 'sender'
     * @param String    $member     Member name like 'TELKOMSEL' or 'IM3' etc
     * @param String    $date       Message date
     * @param String    $status     Message Status
     * @param Int       $price      Message Price
     * @param Int       $count      Message Count
     */
    private function storeSummary(Array &$summary, $group, $member, $date, $status, $price, $count) {
        isset($summary[$group])
           ?: $summary[$group] = [];

        if(!isset($summary[$group][$member])) {
           $startDate = $this->clientTimeZone(strtotime($this->firstDateOfMonth.' 1 second'));
           $endDate = $this->clientTimeZone(strtotime($this->lastDateOfMonth.' -1 second'));
           $period  = $this->getDateRange($startDate, $endDate);
           $traffic = [
                    'd'     => 0,   // Delivered
                    'udC'   => 0,   // Undelivered Charged
                    'udUc'  => 0,   // Undelivered Uncharged
                    'tsC'   => 0,   // Total SMS Charged
                    'ts'    => 0,   // Total SMS
                    'tp'    => 0,   // Total Price
                ];

           $summary[$group][$member] = array_fill_keys($period, $traffic);
        }

        $transaction = &$summary[$group][$member][$date];

        switch($status) {
            case self::SMS_STATUS_DELIVERED:
                    $transaction['d']    += $count;
                    $transaction['tsC']  += $count;
                break;
            case self::SMS_STATUS_UNDELIVERED_CHARGED:
                    $transaction['udC']  += $count;
                    $transaction['tsC']  += $count;
                break;
            case self::SMS_STATUS_UNDELIVERED:
                    $transaction['udUc'] += $count;
                break;
        }

        $transaction['ts'] += $count;
        $transaction['tp'] += $price;
    }




    /**
     * Close and save both Final and Awaiting report Writer handler
     */
    private function saveReportFile() {
        $this->finalReportWriter->close();
        $this->awaitingReportWriter->close();
    }




    /**
     * List of summary color style
     *
     * @return Object   List of summary report style like color, font, background etc,
     */
    private function getSummaryColorStyle() {
        return (object) [
            'bold'  => ['font'      => ['bold' => true]],

            'center'=> [
                        'font'      => ['bold' => true],
                        'alignment' => ['horizontal' => 'center','vertical'   => 'center',],
                    ],

            'black' => [
                        'font' => ['bold' => true,   'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['type' => 'solid','color' => ['rgb' => '000000']]
                    ],

            'gray'  => [
                        'font' => ['bold' => true,   'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['type' => 'solid','color' => ['rgb' => '333333']]
                    ],

            'odd'   => ['fill' => ['type' => 'solid','color' => ['rgb' => 'FF3300']]],
            'even'  => ['fill' => ['type' => 'solid','color' => ['rgb' => '3366FF']]],

            'd'     => ['fill' => ['type' => 'solid','color' => ['rgb' => '99FF00']]],
            'udC'   => ['fill' => ['type' => 'solid','color' => ['rgb' => '66FF33']]],
            'udUc'  => ['fill' => ['type' => 'solid','color' => ['rgb' => 'FFCC99']]],
            'ts'    => ['fill' => ['type' => 'solid','color' => ['rgb' => '33FF99']]],
            'tsC'   => ['fill' => ['type' => 'solid','color' => ['rgb' => '00FFFF']]],
            'tp'    => ['fill' => ['type' => 'solid','color' => ['rgb' => '00CCFF']]],
        ];

    }




    /**
     * Dump symmary data by group 'userId' or 'operator' or 'sender'
     *
     * -----------------|--------------------------------------------------------------------------------------------
     *                  |				OPERATOR							# level 1
     *                  |--------------------------------------------------------------------------------------------
     *     Date         |               TELKOMSEL						DEFAULT			# level 2
     *                  |--------------------------------------------------------------------------------------------
     *                  | D	UD_C	UD_UC	TS	TS_C	TP	D	UD_C	UD_UC	TS	TS_C	TP      # level 3
     * -----------------|--------------------------------------------------------------------------------------------
     * 2017-05-01	| 0	0	0	0	0	0	0	0	0	0	0	0
     * 2017-05-02	| 29	0	0	29	29	0	33	0	0	33	33	0
     * 2017-05-03	| 48	0	1	48	49	0	65	0	0	65	65	0
     * 2017-05-04	| 39	0	0	39	39	0	42	1	0	43	43	0
     * 2017-05-05       | 38	0	0	38	38	0	55	1	0	56	56	0
     * -----------------|--------------------------------------------------------------------------------------------
     *
     * @param String    $type       Group Name
     * @param PHPExcel  $excel      PHP Excel Object
     * @param Array     $data       Summary data list $this->awaitingReportSummary and $this->finalReportSummary
     * @param Int       $startRow   Start row
     */
    private function insertSummaryByCategory($type ,&$excel, &$data, $startRow) {
        $sheet    = $excel->setActiveSheetIndex(0);
        $style    = $this->getSummaryColorStyle();
        $colWidth = count($data) *6;
        $lastCol  = 'G';
        $iterator = 0;
        // Set Column Title level 1
        $sheet  ->setCellValue('A'.$startRow, 'Date') ->mergeCells('A'.$startRow.':A'.($startRow +2))
                ->setCellValue('B'.$startRow, $type);

        $i = 0;
        foreach($data as $group => $traffics) {
            // Set Column Name
            $start    = ($i++ *6) +1;
            $startCol = PHPExcel_Cell::stringFromColumnIndex($start);
            $endCol   = PHPExcel_Cell::stringFromColumnIndex($i *6);
            $col      = [
                            'd' 	=> PHPExcel_Cell::stringFromColumnIndex($start),
                            'udC' 	=> PHPExcel_Cell::stringFromColumnIndex($start +1),
                            'udUc' 	=> PHPExcel_Cell::stringFromColumnIndex($start +2),
                            'ts' 	=> PHPExcel_Cell::stringFromColumnIndex($start +3),
                            'tsC' 	=> PHPExcel_Cell::stringFromColumnIndex($start +4),
                            'tp' 	=> PHPExcel_Cell::stringFromColumnIndex($start +5),
                        ];
            $lastCol  = $col['tp'];


            // Set Column Title level 2 & 3
            $sheet
                ->setCellValue($startCol   . ($startRow +1), $group) 	->mergeCells($startCol.($startRow +1).':'.$endCol.($startRow +1))
                ->setCellValue($col['d']   . ($startRow +2), 'D')
                ->setCellValue($col['udC'] . ($startRow +2), 'UD_C')
                ->setCellValue($col['udUc']. ($startRow +2), 'UD_UC')
                ->setCellValue($col['ts']  . ($startRow +2), 'TS')
                ->setCellValue($col['tsC'] . ($startRow +2), 'TS_C')
                ->setCellValue($col['tp']  . ($startRow +2), 'TP');


            // Set current Group Coulumn Title Style
            $sheet->getStyle($startCol   . ($startRow +1)) ->applyFromArray($i%2 ? $style->even : $style->odd);
            $sheet->getStyle($col['d']   . ($startRow +2)) ->applyFromArray($style->d);
            $sheet->getStyle($col['udC'] . ($startRow +2)) ->applyFromArray($style->udC);
            $sheet->getStyle($col['udUc']. ($startRow +2)) ->applyFromArray($style->udUc);
            $sheet->getStyle($col['ts']  . ($startRow +2)) ->applyFromArray($style->ts);
            $sheet->getStyle($col['tsC'] . ($startRow +2)) ->applyFromArray($style->tsC);
            $sheet->getStyle($col['tp']  . ($startRow +2)) ->applyFromArray($style->tp);


            // Insert Summary per day transaction specific group item
            $iterator = $startRow +2;
            foreach($traffics as $date => $traffic) {
                $sheet
                    ->setCellValue('A' .       ++$iterator, $date)
                    ->setCellValue($col['d']   . $iterator, $traffic['d'])
                    ->setCellValue($col['udC'] . $iterator, $traffic['udC'])
                    ->setCellValue($col['udUc']. $iterator, $traffic['udUc'])
                    ->setCellValue($col['ts']  . $iterator, $traffic['ts'])
                    ->setCellValue($col['tsC'] . $iterator, $traffic['tsC'])
                    ->setCellValue($col['tp']  . $iterator, $traffic['tp']);
            }


            // Write Total of perday transaction
            $sheet
                ->setCellValue('A' .       ++$iterator, 'TOTAL')
                ->setCellValue($col['d']   . $iterator, array_sum(array_column($traffics,'d')))
                ->setCellValue($col['udC'] . $iterator, array_sum(array_column($traffics,'udC')))
                ->setCellValue($col['udUc']. $iterator, array_sum(array_column($traffics,'udUc')))
                ->setCellValue($col['ts']  . $iterator, array_sum(array_column($traffics,'ts')))
                ->setCellValue($col['tsC'] . $iterator, array_sum(array_column($traffics,'tsC')))
                ->setCellValue($col['tp']  . $iterator, array_sum(array_column($traffics,'tp')));
        }

        // Merge Column which contain "group" label
        $sheet->mergeCells('B'.$startRow.':'.$lastCol.$startRow);

        // Center all Column Title
        $sheet->getStyle('A'.$startRow.':'.$lastCol.($startRow +2)) ->applyFromArray($style->center);

        // Fill "Total" row with sytle gray
        $sheet->getStyle('A'.$iterator.':'.$lastCol.$iterator)      ->applyFromArray($style->gray);

        // Fill "Date" and "group" label with style black
        $sheet->getStyle('A'.$startRow.':B'.$startRow)->applyFromArray($style->black);
    }




    /**
     * Generate Summary Report file                                             <br />
     *                                                                          <br />
     * This function would generate 2 report file                               <br />
     * included "Final Report Summary" and "Awaiting Report Summary"            <br />
     *                                                                          <br />
     * use this function after generate detailed report                         <br />
     * this function would get summary data from                                <br />
     * $this->finalReportSummary and $this->awaitingReportSummary               <br />
     *
     *
     * @param String    $fileName   User Detailed Report file name
     * @param Mixed     $userIds    Single User id or list of User id
     */
    private function saveSummary($fileName, $userIds) {
        $dirFinal       = $this->reportDir.'/'. self::DIR_FINAL_REPORT;
        $dirAwaiting    = $this->reportDir.'/'. self::DIR_AWAITING_REPORT;
        $finalReport    = $dirFinal.       '/'.$fileName.$this->periodSuffix.self::SUFFIX_SUMMARY_FINAL_REPORT.'.xlsx';
        $awaitingReport = $dirAwaiting.    '/'.$fileName.$this->periodSuffix.self::SUFFIX_SUMMARY_AWAITING_REPORT.'.xlsx';

        $fReport        = new PHPExcel();
        $aReport        = new PHPExcel();
        $startRow       = 20;
        $userNames      = [];

        foreach(is_array($userIds) ? $userIds : [$userIds] as $userId) {
            $userNames[]= $this->getUserDetail($userId)['USER_NAME'];
        }

        //  set Summary Header
        $this->setSummaryReportHeader($fReport, $userNames, $this->finalReportSummary);
        $this->setSummaryReportHeader($aReport, $userNames, $this->awaitingReportSummary);


        //  insert Sender Summary
        $this->insertSummaryByCategory('SENDER',   $fReport, $this->finalReportSummary   ['senderId'], $startRow);
        $this->insertSummaryByCategory('SENDER',   $aReport, $this->awaitingReportSummary['senderId'], $startRow);


        //  insert Operator Summary
        $this->insertSummaryByCategory('OPERATOR', $fReport, $this->finalReportSummary   ['operator'], $startRow +37);
        $this->insertSummaryByCategory('OPERATOR', $aReport, $this->awaitingReportSummary['operator'], $startRow +37);


        //  insert UserId Summary
        $this->insertSummaryByCategory('USER NAME', $fReport, $this->finalReportSummary   ['userId'], $startRow +74);
        $this->insertSummaryByCategory('USER NAME', $aReport, $this->awaitingReportSummary['userId'], $startRow +74);


        //  set Summary report to auto size column
        $this->setSummaryToAutoSize($fReport);
        $this->setSummaryToAutoSize($aReport);


        //  save Final Report
        $writer = PHPExcel_IOFactory::createWriter($fReport, 'Excel2007');
        $writer->save($finalReport);

        //  save awaiting report
        $writer = PHPExcel_IOFactory::createWriter($aReport, 'Excel2007');
        $writer->save($awaitingReport);

        //unset($fReport, $aReport, $objWriter);
    }




    /**
     * Resize all excel columns to auto size
     *
     * @param PHPExecl  $excel  PHP Excel Object
     */
    private function setSummaryToAutoSize(&$excel) {
        $sheet = $excel->setActiveSheetIndex(0);
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }




    /**
     * Generate Summary Report Header and set excel file information
     * ----------------------------------------------------------------
     * Last Update Date           Wednesday, 07 June 2017 at 09:49
     * User Name                  yamahabdg
     *
     * Delivered                  1413
     * Undelivered (charged)      48
     * Undelivered (uncharged)    5
     * Total SMS                  1466
     * Total SMS Charged          1461
     * Total Price                999999
     * ----------------------------------------------------------------
     *
     * @param PHPExcel  $excel      PHP Excel Object
     * @param Mixed     $userName   UserId
     * @param Array     $data
     */
    private function setSummaryReportHeader(&$excel, $userName, &$data) {
        $sheet     = $excel->setActiveSheetIndex(0);
        $lastCol   = 'G';
        $userNames = is_array($userName) ? implode(', ', $userName) : $userName;
        $style     = $this->getSummaryColorStyle();
        $date      = $this->clientTimeZone(date('Y-m-d H:i:s'),'l, d F Y \a\t H:i');
        $sum       = [
            'd'    => 0,
            'udC'  => 0,
            'udUc' => 0,
            'ts'   => 0,
            'tsC'  => 0,
            'tp'   => 0
        ];

        foreach($data['userId'] as &$traffics) {
            $sum['d']    += array_sum(array_column($traffics,'d'));
            $sum['udC']  += array_sum(array_column($traffics,'udC'));
            $sum['udUc'] += array_sum(array_column($traffics,'udUc'));
            $sum['ts']   += array_sum(array_column($traffics,'ts'));
            $sum['tsC']  += array_sum(array_column($traffics,'tsC'));
            $sum['tp']   += array_sum(array_column($traffics,'tp'));

        }

        $excel->getProperties()
                    ->setCreator('1rstwap')
                    ->setLastModifiedBy('SMS_API_ADMIN')
                    ->setTitle('Billing Report '.$userNames.' on '.date('M_Y', strtotime($this->year.'-'.$this->month)))
                    ->setSubject('Billing Report');

        $sheet
            ->setCellValue('A1',  'Last Update Date')       ->setCellValue('B1', $date)                         ->mergeCells('B1:'  . $lastCol.'1')
            ->setCellValue('A2',  'User Name')              ->setCellValue('B2', $userNames)                    ->mergeCells('B2:'  . $lastCol.'2')

            ->setCellValue('A4',  'Delivered')              ->setCellValue('B4', $sum['d'])                     ->mergeCells('B4:'  . $lastCol.'4')
            ->setCellValue('A5',  'Undelivered (charged)')  ->setCellValue('B5', $sum['udC'])                   ->mergeCells('B5:'  . $lastCol.'5')
            ->setCellValue('A6',  'Undelivered (uncharged)')->setCellValue('B6', $sum['udUc'])                  ->mergeCells('B6:'  . $lastCol.'6')
            ->setCellValue('A7',  'Total SMS')              ->setCellValue('B7', $sum['ts'])                    ->mergeCells('B7:'  . $lastCol.'7')
            ->setCellValue('A8',  'Total SMS Charged')      ->setCellValue('B8', $sum['tsC'])                   ->mergeCells('B8:'  . $lastCol.'8')
            ->setCellValue('A9',  'Total Price')            ->setCellValue('B9', $sum['tp'])                    ->mergeCells('B9:'  . $lastCol.'9')

             // Legend
            ->setCellValue('A11', 'Legend')                                                                     ->mergeCells('A11:' . $lastCol.'11')
            ->setCellValue('A12', 'D')                      ->setCellValue('B12', 'Delivered')			->mergeCells('B12:' . $lastCol.'12')
            ->setCellValue('A13', 'UD_C')                   ->setCellValue('B13', 'Undelivered (Charged)')	->mergeCells('B13:' . $lastCol.'13')
            ->setCellValue('A14', 'UD_UC')                  ->setCellValue('B14', 'Undelivered (Uncharged)')	->mergeCells('B14:' . $lastCol.'14')
            ->setCellValue('A15', 'TS')                     ->setCellValue('B15', 'Total SMS')			->mergeCells('B15:' . $lastCol.'15')
            ->setCellValue('A16', 'TS_C')                   ->setCellValue('B16', 'Total SMS Charged')		->mergeCells('B16:' . $lastCol.'16')
            ->setCellValue('A17', 'TP')                     ->setCellValue('B17', 'Total Price')		->mergeCells('B17:' . $lastCol.'17');


        // Set legend color
        $sheet->getStyle('A11') ->applyFromArray($style->center);
        $sheet->getStyle('A11') ->applyFromArray($style->black);
        $sheet->getStyle('A12') ->applyFromArray($style->d);
        $sheet->getStyle('A13') ->applyFromArray($style->udC);
        $sheet->getStyle('A14') ->applyFromArray($style->udUc);
        $sheet->getStyle('A15') ->applyFromArray($style->ts);
        $sheet->getStyle('A16') ->applyFromArray($style->tsC);
        $sheet->getStyle('A17') ->applyFromArray($style->tp);
    }




    /**
     * Write message into "detailed billing report"
     * could insert into both Final and Awaiting report and
     * could insert into specific Report by defined $type parameters
     *
     * @param Array     $messages   List of user messages
     * @param String    $type       type of message
     */
    private function insertIntoReportFile(Array &$messages, $type = 'all') {
        $final    = strtolower($type) == 'all' ||  strtolower($type) == 'final';
        $awaiting = strtolower($type) == 'all' ||  strtolower($type) == 'awaiting';
        !$final    ?: $this->finalReportWriter->addRows($messages);
        !$awaiting ?: $this->awaitingReportWriter->addRows($messages);
    }




    /**
     * Create Zip package
     * Will Create an user or a group or all package file if
     *
     * @param String    $fileName   Report file name
     */
    private function createReportPackage($fileName = '*') {
        $dirFinal        = $this->reportDir.'/'. self::DIR_FINAL_REPORT;
        $dirAwaiting     = $this->reportDir.'/'. self::DIR_AWAITING_REPORT;
        $finalReport     = $dirFinal.       '/'.$fileName.$this->periodSuffix.'*.xlsx';
        $awaitingReport  = $dirAwaiting.    '/'.$fileName.$this->periodSuffix.'*.xlsx';

        $fileName        = $fileName == '*' ? self::ALL_REPORT_PACKAGE : $fileName;
        $finalPackage    = $dirFinal.       '/'.$fileName.$this->periodSuffix.'.zip';
        $awaitingPackage = $dirAwaiting.    '/'.$fileName.$this->periodSuffix.self::SUFFIX_AWAITING_REPORT.'.zip';

        exec('zip -j '.$finalPackage   .' '.$finalReport);
        exec('zip -j '.$awaitingPackage.' '.$awaitingReport);
    }




    /**
     * Debuging helper,
     * this function created to make debuging more fast and easier;
     *
     * example:
     *      $this->debug(get_defined_vars());
     *      $this->debug(compact('fileName','appliedPrice','trafficCount'));
     *
     * @param Mixed     Any known variable
     */
    private function debug($var = []) {

        $this
            ->queryHistory['TotalExecutionTime'] =
                array_sum(
                    array_map(
                        function($a) {
                            return $a['executionTime'];
                        },
                        $this->queryHistory
                    )
                );
        $this
            ->queryHistory['TotalAllQueryRecords'] =
                array_sum(
                    array_map(
                        function($a) {
                            return $a['totalRecords'];
                        },
                        $this->queryHistory
                    )
                );

        die(json_encode([
                'var'    => $var,
                'query'  => $this->queryHistory,
                'memory' => (memory_get_peak_usage(1) /1024/1024).' MB',
            ], 192).PHP_EOL);
    }



    /**
     * Generate Billing Report File
     * This function will generate 4 billing report for single user
     * included "Detailed Final Status Report", "Summary Final Status Report"
     *          "Detailed Awaiting Status Report" and "Summary Awaiting Status Report"
     *
     * This function also generate 2 package per user inform zip file
     * included "{$userName}.zip" which contain all "Final Status Report"
     * and      "{$userName}_Include_Awaiting_Dr.zip" which contain all "Awaiting Report"
     *
     *
     * This function will collect all report on single package file for 2 type.
     * for example:
     *      BILLING_REPORT_May_2017.zip
     *      BILLING_REPORT_May_2017_Include_Awaiting_Dr.zip
     */
    public function generate() {
        echo "\033[1;32m-------------------[ START GENERATE REPORT ".$this->periodSuffix.']-------------------'.PHP_EOL;
        echo "\033[1;97mPERIOD\tSTATUS\tPROFILE ID\tTYPE\t\tREPORT NAME".PHP_EOL;
        $scriptRunningTime = $this->getMicroTime();
        if($this->lastFinalStatusDate !== false) {

            $this->unchargedDeliveryStatus = implode('\',\'', array_keys($this->getDeliveryStatus(self::SMS_STATUS_UNCHARGED)));
            $users                       = $this->getUserDetail();
            $prevBillingProfileId        = null;
            $excludedUser                = [];
            $newUserLastSendDate         = [];

            /**
             * Start Generate user's report
             */
            foreach($users as &$user) {
                $fileName              = $user['USER_NAME'];
                $userName              = $user['USER_NAME'];
                $userId                = $user['USER_ID'];
                $userBillingProfileId  = $user['BILLING_PROFILE_ID'];
                $userTieringGroupId    = $user['BILLING_TIERING_GROUP_ID'];
                $userTieringGroup      = null;
                $userReportGroupId     = $user['BILLING_REPORT_GROUP_ID'];
                $userReportGroup       = null;
                $getByGroup            = false;
                $userReportGroupDates  = [];

                if(is_null($userId) || in_array($userId, $excludedUser)) continue;

                $this->log->debug('Validate Billing Profile for User "'.$userName.'"');
                $this->log->debug('Load last message date');

                $userLastSendDate      = $this->loadLastMessageDate($userId);
                $this->log->debug('got '.$userLastSendDate);
                $counter               = 0;


                /* =======================================
                 *  Get User billing information
                 * ======================================= */
                $this->log->debug('check user billing information');
                if(is_null($userBillingProfileId)) {
                    $this->log->warn('User '.$userName.' was not assigned to any Billing Profile.');
                    $this->log->info('Skip generate report for user '.$userName);
                    continue;
                }

                $this->log->debug('get user billing information');
                $userBillingProfile    = $this->loadBillingProfileCache($userBillingProfileId);

                $this->log->debug('check user billing detail');
                if(is_null($userBillingProfile)) {
                    $this->log->warn('User '.$userName.' was assigned to Billing Profile '.$userBillingProfileId.' but not found on any detail on '.DB_BILL_PRICELIST.'.BILLING_PROFILE');
                    $this->log->info('Skip generate report for user '.$userName);
                    continue;
                }

                /* =======================================
                 *  End Of Get User billing information
                 * ======================================= */


                /*=======================================
                 * Get Report Group information
                 *=======================================*/
                $this->log->debug('check user report group');
                if(!empty($userReportGroupId)) {
                    $this->log->debug('get user report group information '.$userReportGroupId);
                    $userReportGroup = $this->loadReportGroupCache($userReportGroupId);
                    if(is_null($userReportGroup)) {
                        $this->log->warn('User '.$userName.' was assigned to Billing Profile "'.$userReportGroupId.'" but not found billing profile detail on '.DB_BILL_PRICELIST.'.BILLING_REPORT_GROUP');
                        $this->log->info('Skip generate report for user '.$userName);
                        continue;
                    }

                    $getByGroup   = true;
                    $fileName     = $userReportGroup['NAME'];
                    $this->log->debug('get list of user on report group '.$fileName);
                    $userGroups   = $this->getReportGroupUserList($userReportGroupId);
                    $userId       = array_merge([$userId], array_column($userGroups, 'USER_ID'));
                    $excludedUser = array_merge($excludedUser, $userId);
                    foreach($userId as $id) {
                        $userReportGroupDates[$id] = $this->loadLastMessageDate($id);
                    }
                }
                /* =======================================
                 *  End of Get report Group information
                 * ======================================= */


                /* =============================================================
                 *  Start get User messages and insert into report file
                 * ============================================================= */
                $this->log->debug('Checking new message for user '.$userName);
                $hasNewMessage = !empty(
                                    $getByGroup
                                    ? $this->getGroupMessageStatus($userReportGroupDates, $this->lastFinalStatusDate, 1, 0)
                                    : $this->getUserMessageStatus ($userId, $userLastSendDate, $this->lastFinalStatusDate, 1, 0)
                                );

                if($hasNewMessage) {
                    echo "\033[1;32m".$this->year.'-'.$this->month."\tGenerate\t".$userBillingProfileId."\t".$userBillingProfile['BILLING_TYPE']." \t".$fileName.PHP_EOL;

                    if(strtoupper($userBillingProfile['BILLING_TYPE']) == self::BILLING_OPERATOR_BASE) {
                        $operatorPrice  = &$userBillingProfile['PRICING'];
                        $operatorPrefix = &$userBillingProfile['PREFIX'];

                        /**
                         * OPERATOR BASE - Final status messages
                         */
                        $lastSendDate = $this->firstDateOfMonth;
                        $this->createReportFile($fileName, $isNewFile);

                        /**
                         * If the final status report doesn't exists
                         * $userLastSendDate or $userReportGroup change to the firstDateOfMonth
                         */
                        if($isNewFile){
                            if($getByGroup){
                                foreach($userReportGroupDates as &$date){
                                    $date = $this->firstDateOfMonth;
                                }
                            } else {
                                $userLastSendDate = $this->firstDateOfMonth;
                            }
                        }

                        do {
                            $this->log->debug('Get '.$fileName.' messages from '.$counter .' to '.($counter + REPORT_PER_BATCH_SIZE));
                            $messages = $getByGroup
                                            ? $this->getGroupMessageStatus($userReportGroupDates,      $this->lastFinalStatusDate, REPORT_PER_BATCH_SIZE, $counter)
                                            : $this->getUserMessageStatus ($userId, $userLastSendDate, $this->lastFinalStatusDate, REPORT_PER_BATCH_SIZE, $counter);

                            if(!empty($messages)){
                                $lastSendDate  = end($messages)['RECEIVE_DATETIME'];
                                $this->assignMessagePrice(self::BILLING_OPERATOR_BASE, $messages, $operatorPrice, $operatorPrefix);
                                $this->insertIntoReportFile($messages);
                                $this->getMessageSummary($messages);
                                $counter      += REPORT_PER_BATCH_SIZE;
                            }
                        } while(!empty($messages) && count($messages) == REPORT_PER_BATCH_SIZE);


                        /**
                         * OPERATOR BASE - Update user last send date time
                         */
                        if($getByGroup) {
                            $userReportGroupDates = $this->getGroupLastSendDate($userId);
                            foreach($userReportGroupDates as $userId => $lastSendDate) {
                                $newUserLastSendDate[$userId] = $lastSendDate;
                            }
                        }
                        else {
                            $newUserLastSendDate[$userId] = $lastSendDate;
                        }


                        /**
                         * OPERATOR BASE - Including Awaiting DR
                         */
                        $counter  = 0;
                        do {
                            $this->log->debug('Get '.$fileName.' awaiting_dr messages from '.$counter .' to '.($counter + REPORT_PER_BATCH_SIZE));
                            $messages = $getByGroup
                                            ? $this->getGroupMessageStatus($userReportGroupDates,  $this->lastDateOfMonth, REPORT_PER_BATCH_SIZE, $counter)
                                            : $this->getUserMessageStatus ($userId, $lastSendDate, $this->lastDateOfMonth, REPORT_PER_BATCH_SIZE, $counter);

                            if(!empty($messages)) {
                                $this->assignMessagePrice(self::BILLING_OPERATOR_BASE, $messages, $operatorPrice, $operatorPrefix);
                                $this->insertIntoReportFile($messages, 'awaiting');
                                $this->getMessageSummary   ($messages, 'awaiting');
                                $counter += REPORT_PER_BATCH_SIZE;
                            }
                        } while(!empty($messages) && count($messages) == REPORT_PER_BATCH_SIZE);
                    }
                    else if(strtoupper($userBillingProfile['BILLING_TYPE']) == self::BILLING_TIERING_BASE) {

                        /**
                         * Get TIERING Traffics
                         */
                        if(is_null($userTieringGroupId)) {
                            $finalTieringTraffic    = $this->getTieringTraffic($userId);
                            $awaitingTieringTraffic = $this->getTieringTraffic($userId, true);
                        }
                        else {
                            $tieringGroupUserList   = $this->getTieringGroupUserList($userTieringGroupId);
                            $finalTieringTraffic    = $this->getTieringTraffic($tieringGroupUserList);
                            $awaitingTieringTraffic = $this->getTieringTraffic($tieringGroupUserList, true);
                        }

                        $this->log->debug('Got '.$fileName.' tiering traffic on '.$this->year.'-'.$this->month.' with status final: '.$finalTieringTraffic.' and awaiting: '.$awaitingTieringTraffic);

                        $finalPrice     = $this->getTieringPriceByTraffic($userBillingProfile['PRICING'], $finalTieringTraffic);
                        $awaitingPrice  = $this->getTieringPriceByTraffic($userBillingProfile['PRICING'], $awaitingTieringTraffic);
                        $operatorPrefix = $this->getOperatorDialPrefix(self::OPERATOR_INDONESIA);
                        $this->log->debug('Applied Price: Final = '.json_encode($finalPrice).' | Awaiting = '.json_encode($awaitingPrice));

                        $this->createReportFile($fileName, $isNewFile, compact('finalPrice','awaitingPrice'));

                        /**
                         * If the final status report doesn't exists
                         * $userLastSendDate or $userReportGroup change to the firstDateOfMonth
                         */
                        if($isNewFile){
                            if($getByGroup){
                                foreach($userReportGroupDates as &$date){
                                    $date = $this->firstDateOfMonth;
                                }
                            } else {
                                $userLastSendDate = $this->firstDateOfMonth;
                            }
                        }

                        /**
                         * TIERING BASE - Dump Final And Awaiting DR SMS
                         */
                        do {
                            $this->log->debug('Get '.$fileName.' messages from '.$counter .' to '.($counter + REPORT_PER_BATCH_SIZE));
                            $messages = $getByGroup
                                            ? $this->getGroupMessageStatus($userReportGroupDates,      $this->lastFinalStatusDate, REPORT_PER_BATCH_SIZE, $counter)
                                            : $this->getUserMessageStatus ($userId, $userLastSendDate, $this->lastFinalStatusDate, REPORT_PER_BATCH_SIZE, $counter);

                            if(!empty($messages)){
                                $lastSendDate  = end($messages)['RECEIVE_DATETIME'];
                                // TIERING BASE - Final
                                $this->assignMessagePrice(self::BILLING_TIERING_BASE, $messages, $finalPrice, $operatorPrefix);
                                $this->insertIntoReportFile($messages, 'final');
                                $this->getMessageSummary   ($messages, 'final');

                                // TIERING BASE - Awaiting
                                $this->assignMessagePrice(self::BILLING_TIERING_BASE, $messages, $awaitingPrice, $operatorPrefix);
                                $this->insertIntoReportFile($messages, 'awaiting');
                                $this->getMessageSummary   ($messages, 'awaiting');

                                $counter      += REPORT_PER_BATCH_SIZE;
                            }
                        } while(!empty($messages) && count($messages) == REPORT_PER_BATCH_SIZE);

                        /**
                         * TIERING BASE - Update user last send date time
                         */
                        if($getByGroup) {
                            $userReportGroupDates = $this->getGroupLastSendDate($userId);
                            foreach($userReportGroupDates as $userId => $lastSendDate) {
                                $newUserLastSendDate[$userId] = $lastSendDate;
                            }
                        }
                        else {
                            $newUserLastSendDate[$userId] = $lastSendDate;
                        }

                        /**
                         * TIERING BASE - Awaiting DR SMS
                         */
                        $counter = 0;
                        do {
                            $this->log->debug('Get '.$fileName.' messages from '.$counter .' to '.($counter + REPORT_PER_BATCH_SIZE));
                            $messages = $getByGroup
                                            ? $this->getGroupMessageStatus($userReportGroupDates,      $this->lastDateOfMonth, REPORT_PER_BATCH_SIZE, $counter)
                                            : $this->getUserMessageStatus ($userId, $lastSendDate, $this->lastDateOfMonth, REPORT_PER_BATCH_SIZE, $counter);
                            if(!empty($messages)){
                                // TIERING BASE - Awaiting
                                $this->assignMessagePrice(self::BILLING_TIERING_BASE, $messages, $awaitingPrice, $operatorPrefix);
                                $this->insertIntoReportFile($messages, 'awaiting');
                                $this->getMessageSummary   ($messages, 'awaiting');
                                $counter += REPORT_PER_BATCH_SIZE;
                            }
                        } while(!empty($messages) && count($messages) == REPORT_PER_BATCH_SIZE);


                    }

                    if(in_array(
                            strtoupper($userBillingProfile['BILLING_TYPE']),
                            [self::BILLING_OPERATOR_BASE, self::BILLING_TIERING_BASE]
                        )
                    ) {
                        $userId = $getByGroup ? array_keys($userReportGroupDates) : $userId;
                        $this->saveReportFile();

                        $getByGroup
                                ? $this->saveSummary($fileName, array_keys($userReportGroupDates))
                                : $this->saveSummary($fileName, $userId);

                        $this->createReportPackage($fileName);
                    }
                }
                else {
                    echo "\033[1;31m".$this->year.'-'.$this->month."\tSkipped  \t".$userBillingProfileId."\t".$userBillingProfile['BILLING_TYPE']." \t".$fileName.PHP_EOL;
                    $this->log->info('Skip generate report for '.$fileName.', No new messages found.');
                }

                $this->createReportPackage();

                /* =============================================================
                 *  End Of get User messages and insert into report file
                 * =============================================================*/

            }

            // Update last message send date time
            $this->saveLastMessageDate($newUserLastSendDate);
            $this->log->info('Finish generate report for '.$this->year.'-'.$this->month.' period');


            // Calucate peformance for generating
            $scriptRunningTime = number_format($this->getMicroTime() - $scriptRunningTime, 2).' sec';
            $averageMemory     = number_format(array_sum(array_column($this->queryHistory,'currentMemoryUsed'))  / count($this->queryHistory), 2).' MB';

            $totalQueryRecord  = number_format(array_sum(array_column($this->queryHistory,'totalRecords')));
            $averageRecords    = number_format(array_sum(array_column($this->queryHistory,'totalRecords'))       / count($this->queryHistory), 2);

            $totalQueryTime    = number_format(array_sum(array_column($this->queryHistory,'executionTime')), 2).' sec';
            $averageExecTime   = number_format(array_sum(array_column($this->queryHistory,'executionTime'))      / count($this->queryHistory), 2).' sec';

            $this->log->info("Peformance:"
                        ."\t TotalQueryRecords: "   .$totalQueryRecord
                        ."\t AverageQueryRecords: " .$averageRecords
                        ."\t TotalQueryTime: "      .$totalQueryTime
                        ."\t AverageQueryTime: "    .$averageExecTime
                        ."\t AverageMemoryUsage: "  .$averageMemory
                        ."\t runningTime: "         .$scriptRunningTime
                );
        }
        else {
            $this->log->info('Skip generate report, there is no final status message.');
        }
    }




    /**
     * Generate file name for user report                                           <br />
     * the file name would countain current period who set on constructor section
     *
     * @param   Bool    $awaiting   Is report would including sms which still awaiting for final status
     * @param   Int     $userId     User Id if want to download for specific user, or set to null for download all report
     * @return  String
     */
    private function getReportFileName($awaiting = false, $userId = null) {
        $dir      = $this->reportDir;
        $fileName = self::ALL_REPORT_PACKAGE;

        if(!is_null($userId) && is_numeric((int)$userId)) {
            $user = $this->getUserDetail($userId);
            if(empty($user)) {
                return null;
            }

            if(!is_null($user['BILLING_REPORT_GROUP_ID'])) {
                $group    = $this->loadReportGroupCache($user['BILLING_REPORT_GROUP_ID']);
                $fileName = $group['NAME'];
            }
            else {
                $fileName = $user['USER_NAME'];
            }
        }

        $fileName     .= $this->periodSuffix;

        if($awaiting) {
            $dir      .= '/'.self::DIR_AWAITING_REPORT;
            $fileName .= self::SUFFIX_AWAITING_REPORT;
        }
        else {
            $dir      .= '/'.self::DIR_FINAL_REPORT;
        }

        return preg_replace('/ +/','_', $dir.'/'.$fileName.'.zip');
    }




    /**
     * Function to update table USER based on specified COLUMN to update
     *
     * @param Array $data ['column','value','whereClause']
     * @return Array
     */
    public function updateUser($data){
        $updateColumn  = $data['column'];
        $updateValue   = $data['value'];
        $whereClause   = $data['whereClause'];

        return $this->exec_query(
                    ' UPDATE '.DB_SMS_API_V2.'.USER '
                    .' SET '.$updateColumn.'  = '.$updateValue.''
                    .' WHERE '.$whereClause.''
                );
    }




    /**
     * Function to insert new operator setting into Table BILLING_PROFILE_OPERATOR
     *
     * @param Int $billingProfileID
     * @param Int $operatorID
     * @param Int $price
     * @return Array
     */
    public function insertToOperator($billingProfileID, $operatorID, $price){
        return $this->exec_query(
                 ' INSERT INTO '.DB_BILL_PRICELIST.'.BILLING_PROFILE_OPERATOR '
                .' (BILLING_PROFILE_OPERATOR_ID, BILLING_PROFILE_ID, OP_ID, PER_SMS_PRICE) '
                .' VALUES (NULL, '.$billingProfileID.', "'.$operatorID.'", '.$price.') '
            );
    }




    /**
     * Function to insert new tiering setting into Table BILLING_PROFILE_TIERING
     *
     * @param Int $billingProfileID
     * @param Int $smsCountFrom
     * @param Int $smsCountUpTo
     * @param Int $price
     * @return Array
     */
    public function insertToTiering($billingProfileID, $smsCountFrom, $smsCountUpTo, $price){
        return $this->exec_query(
                 ' INSERT INTO '.DB_BILL_PRICELIST.'.BILLING_PROFILE_TIERING '
                .' (BILLING_PROFILE_TIERING_ID, BILLING_PROFILE_ID, SMS_COUNT_FROM, SMS_COUNT_UP_TO, PER_SMS_PRICE) '
                .' VALUES (NULL, '.$billingProfileID.', "'.$smsCountFrom.'", "'.$smsCountUpTo.'", '.$price.') '
            );
    }




    /**
     * Function to insert new Billing Profile into Table BILLING_PROFILE
     *
     * @param String $name
     * @param String $billingType
     * @param String $description
     * @return Array
     */
    public function insertToBillingProfile($name, $billingType, $description){
        return $this->exec_query(
                     ' INSERT INTO '.DB_BILL_PRICELIST.'.BILLING_PROFILE '
                    .' (BILLING_PROFILE_ID, NAME, BILLING_TYPE, DESCRIPTION, CREATED_AT, UPDATED_AT) '
                    .' VALUES (NULL, "'.$name.'", "'.$billingType.'", "'.$description.'", now(), now()) '
                );
    }




    /**
     * Function to insert new Tiering Group into Table BILLING_TIERING_GROUP
     *
     * @param String $name
     * @param String $description
     * @return Array
     */
    public function insertToTieringGroup($name,  $description){
        return $this->exec_query(
                     ' INSERT INTO '.DB_BILL_PRICELIST.'.BILLING_TIERING_GROUP '
                    .' (BILLING_TIERING_GROUP_ID, NAME, DESCRIPTION, CREATED_AT, UPDATED_AT) '
                    .' VALUES (NULL, "'.$name.'", "'.$description.'", now(), now()) '
                );
    }




    /**
     * Function to insert new Report Group into Table BILLING_REPORT_GROUP
     *
     * @param String $name
     * @param String $description
     * @return Array
     */
    public function insertToReportGroup($name,  $description){
        return $this->exec_query(
                     ' INSERT INTO '.DB_BILL_PRICELIST.'.BILLING_REPORT_GROUP '
                    .' (BILLING_REPORT_GROUP_ID, NAME, DESCRIPTION, CREATED_AT, UPDATED_AT) '
                    .' VALUES (NULL, "'.preg_replace('/ +/', '_', $name).'", "'.$description.'", now(), now()) '
                );
    }




    /**
     * Function to update existing Billing Profile based on updated column
     *
     * @param Int $id
     * @param String $name
     * @param String $billingType
     * @param String $description
     * @return Array
     */
    public function updateBillingProfile($id, $name, $billingType, $description){
        return $this->exec_query(
                     ' UPDATE '.DB_BILL_PRICELIST.'.BILLING_PROFILE '
                    .' SET NAME = "'.$name.'", BILLING_TYPE = "'.$billingType.'", DESCRIPTION = "'.$description.'", UPDATED_AT = now()'
                    .' WHERE BILLING_PROFILE_ID = '.$id.''
                );
    }




    /**
     * Function to update existing Tiering Group based on updated column
     *
     * @param Int $id
     * @param String $name
     * @param String $description
     * @return Array
     */
    public function updateTieringGroup($id, $name, $description){
        return $this->exec_query(
                     ' UPDATE '.DB_BILL_PRICELIST.'.BILLING_TIERING_GROUP '
                    .' SET NAME = "'.$name.'", DESCRIPTION = "'.$description.'", UPDATED_AT = now()'
                    .' WHERE BILLING_TIERING_GROUP_ID = '.$id.''
                );
    }




    /**
     * Function to update existing Report Group based on updated column
     * @param Int $id
     * @param String $name
     * @param String $description
     * @return Array
     */
    public function updateReportGroup($id, $name, $description){
        return $this->exec_query(
                     ' UPDATE '.DB_BILL_PRICELIST.'.BILLING_REPORT_GROUP '
                    .' SET NAME = "'.preg_replace('/ +/', '_', $name).'", DESCRIPTION = "'.$description.'", UPDATED_AT = now()'
                    .' WHERE BILLING_REPORT_GROUP_ID = '.$id.''
                );
    }




    /**
     * Function to delete Billing Profile based on Billing Profile ID
     *
     * @param int $billingProfileID
     * @return Array
     */
    public function deleteBillingProfile($billingProfileID){
         return $this->exec_query(
                        ' DELETE FROM '.DB_BILL_PRICELIST.'.BILLING_PROFILE '
                       .' WHERE BILLING_PROFILE_ID = '.$billingProfileID.''
                );
    }




    /**
     * Function to delete existing billing profile operator based on billing profile ID
     *
     * @param Int $billingProfileID
     * @return Array
     */
    public function deleteBillingProfileOperator($billingProfileID){
        return $this->exec_query(
                        ' DELETE FROM '.DB_BILL_PRICELIST.'.BILLING_PROFILE_OPERATOR '
                       .' WHERE BILLING_PROFILE_ID = '.$billingProfileID.''
                );
    }




    /**
     * Function to delete existing billing profile tiering based on billing profile ID
     *
     * @param Int $billingProfileID
     * @return Array
     */
    public function deleteBillingProfileTiering($billingProfileID){
        return $this->exec_query(
                        ' DELETE FROM '.DB_BILL_PRICELIST.'.BILLING_PROFILE_TIERING '
                       .' WHERE BILLING_PROFILE_ID = '.$billingProfileID.''
                );
    }




    /**
     * Function to delete existing Tiering Group based on tiering group ID
     *
     * @param Int $tieringGroupID
     * @return Array
     */
    public function deleteTieringGroup($tieringGroupID){
        return $this->exec_query(
                        ' DELETE FROM '.DB_BILL_PRICELIST.'.BILLING_TIERING_GROUP '
                       .' WHERE BILLING_TIERING_GROUP_ID = '.$tieringGroupID.''
                );
    }




    /**
     * Function to deletge report group based on report group ID
     *
     * @param Int $reportGroupID
     * @return Array
     */
    public function deleteReportGroup($reportGroupID){
        return $this->exec_query(
                        ' DELETE FROM '.DB_BILL_PRICELIST.'.BILLING_REPORT_GROUP '
                       .' WHERE BILLING_REPORT_GROUP_ID = '.$reportGroupID.''
                );
    }




    /**
     * Check if report file is avaialbe
     *
     * @param   Bool  $awaiting   Is report would including sms which still awaiting for final status
     * @param   Int   $userId     User Id if want to download for specific user, or set to null for download all report
     * @return  Bool
     */
    public function isReportExist($awaiting = false, $userId = null) {
        $fileName = $this->getReportFileName($awaiting, $userId);
        $this->log->debug('check report file: '.$fileName);
        return !is_null($fileName)
                    ? file_exists($fileName)
                    : false;
    }




    /**
     * Download report
     *
     * @param   Bool  $awaiting   Is report would including sms which still awaiting for final status
     * @param   Int   $userId     User Id if want to download for specific user, or set to null for download all report
     */
    public function downloadReport($awaiting = false, $userId = null) {
        $fileName = $this->getReportFileName($awaiting, $userId);
        $this->log->debug('downloading '.$fileName);
        if($this->isReportExist($awaiting, $userId)) {

            // Zip transfer
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: public');
            header('Content-Description: File Transfer');
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileName).'"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($fileName));
            ob_end_flush();
            @readfile($fileName);
        }
        else {
            echo 'Report not found. it may have been deleted.';
            $this->log->warn('Could not download report, file not found: '.$fileName);
        }
    }


    /**
     * Convert DateTime from server timezone to client timezone
     *
     * @param  String $value
     * @return String
     */
    public function clientTimeZone($value, $format='Y-m-d H:i:s'){

        $value = $this->parseDatetimeInput($value);

        // Create datetime based on input value (GMT)
        $date = new \DateTime($value, new \DateTimeZone($this->timezoneServer));

        // Return datetime corrected for client's timezone (GMT+7)
        return $date->setTimezone(new \DateTimeZone($this->timezoneClient))->format($format);
    }


    /**
     * Convert DateTime from server timezone to client timezone
     *
     * @param  String $value
     * @return String
     */
    public function serverTimeZone($value, $format='Y-m-d H:i:s'){

        $value = $this->parseDatetimeInput($value, true);

        $date = new \DateTime($value, new \DateTimeZone($this->timezoneClient));

        return $date->setTimezone(new \DateTimeZone($this->timezoneServer))->format($format);
    }


    /**
     * Parse the dateTime input value
     * numeric input value as a timestamp value and will convert to default format time
     * incorrect value will set to client timezone if parameter isServerTimeZone true
     *
     * @param mixed $value
     * @param boolean $isServerTimeZone
     * @return String
     */
    private function parseDatetimeInput($value, $isServerTimeZone = false){
        // If input value is a unix timestamp
        if (is_numeric($value)) {
            $value = date('Y-m-d H:i:s', $value);
        }

        // If input value is not a correct datetime format
        if(!strtotime($value)){
            $currentTimestamp = $isServerTimeZone ? strtotime($this->timezoneClient.' hours') : strtotime('now');
            $value = date('Y-m-d H:i:s', $currentTimestamp);
        }

        return $value;
    }

}
