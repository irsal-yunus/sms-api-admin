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
 * @author Basri.Yasin
 */
require_once dirname(dirname(__DIR__)).'/configs/config.php';
require_once dirname(dirname(__DIR__)).'/classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';

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
     * SMS Stauts
     */
    const   SMS_STATUS_ALL                  = 'STATUS_ALL';
    const   SMS_STATUS_CHARGED              = 'STATUS_CHARGED';
    const   SMS_STATUS_UNCHARGED            = 'STATUS_UNCHARGED';
    
    
    /**
     * SMS encoding type, affect the way to calculate SMS Length
     */
    const   SMS_TYPE_GSM_7BIT               = 'GSM_7BIT';
    const   SMS_TYPE_UNICODE                = 'UNICODE';

    
    /**
     * SMS Legth by for every type
     */
    const   GSM_7BIT_SINGLE_SMS             = 160;
    const   GSM_7BIT_MULTIPLE_SMS           = 153;
    const   UNICODE_SINGLE_SMS              = 70;
    const   UNICODE_MULTIPLE_SMS            = 67;
    
    
    /**
     * Default value for undefined properties
     */
    const   DEFAULT_OPERATOR                = 'DEFAULT';
    const   DEFAULT_DELIVERY_STATUS         = 'Undefined Message Status';
    

    /**
     * Cache file name
     */
    const   CACHE_LAST_DATE                 = 'lastSendDateTime.lfu';
    const   CACHE_BILLING_PROFILE           = 'billingProfiles.lfu';
    const   CACHE_REPORT_GROUP              = 'reportGroups.lfu';
    
    
    /**
     * Query mode
     */
    const   QUERY_ALL                       = '';
    const   QUERY_SINGLE_ROW                = 'SINGLE_ROW';
    const   QUERY_SINGLE_COLUMN             = 'SINGLE_COLUMN';
    const   QUERY_SINGLE_ROW_AND_COLUMN     = 'SINGLE_ROW_AND_COLUMN';
    
    
    /**
     * 
     */
    const   DIR_FINAL_REPORT                = 'FINAL_STATUS';
    const   DIR_AWAITING_DR_REPORT          = 'INCLUDE_AWAITING_DR';
    
    
    /**
     * Character set for GSM 7Bit
     * for check if the sms whether Latin or Unicode encoded
     */
    const   GSM_7BIT_CHARS                  = '\\\@£\$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !"#¤%&\'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà^{}[~]|€';
    
    
    /**
     * Private properties
     * @var     PDO     $db                 Database connection handler
     */
    private $db,
            $reportDir,
            $reportFileHandler,
            
            $month,
            $year,
            $firstDateOfMonth,
            $lastDateOfMonth,
            $lastFinalStatusDate,
            
            $today,
            $currentFirstDate,
            $currentYear,
            $currentMonth,
            $currentDay
            ;

    
    /**
     * Public properties
     * 
     * @var     Logger  $log handler
     * @var     Array   $queryHistory       History of SQL syntax, total records and execution time
     * @var     Array   $deliveryStatus     Delivery status list for which splitted by CHARGED and UNCHARGED SMS
     */
    public  $log,
            $counter,
            $queryHistory,
            $deliveryStatus, 
            $operator;

    
    /**
     * Api Report constructor
     */
    public function __construct($year = null, $month = null) {
        $this->log               = Logger::getLogger(get_class($this));
        
        $this->year              = $year  ?: date('Y');
        $this->month             = $month ?: date('m');
        $this->reportDir         = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT.'/'.$this->year.'/'.$this->month;
        $this->configureBillingPeriod();
        $this->prepareReportDir();
      
        $this->queryHistory      = [];
        $this->counter           = ['charged' => 0, 'uncharged'];
                
        $this->db                = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
        $this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        
        
        $this->deliveryStatus    = $this->getDeliveryStatus();
    }


    private function configureBillingPeriod() {
        $this->today               = date('Y-m-d 23:59:59');
        $this->currentFirstDate    = date('Y-m-01');
        $this->currentDay          = (int) date('d');
        $this->currentMonth        = date('m');
        $this->currentYear         = date('Y');
        
        $this->firstDateOfMonth    = date('Y-m-01 00:00:00', strtotime($this->year.'-'.$this->month.'-01'));
        $this->lastDateOfMonth     = date('Y-m-t 00:00:00',  strtotime($this->year.'-'.$this->month.'-01'));
        $this->lastFinalStatusDate = $this->month != $this->currentMonth
                                        ? $this->currentDay < 3
                                            ? date('Y-m-d 23:59:59', strtotime($this->today. ' -2 days'))
                                            : date('Y-m-t 23:59:59', strtotime($this->year.  '-'.$this->month.'-01'))
                                        : $this->currentDay >= 3
                                            ? date('Y-m-d 23:59:59', strtotime('-2 days')) 
                                            : false;
        
    }
    
    
    private function prepareReportDir() {
        if (!@is_dir($this->reportDir)) {            
            $this->log->info('Create Report directory "'.$this->reportDir.'"');
            if(!@mkdir($this->reportDir, 0777, TRUE)){
                $this->log->error('Could not create Report directory "'.$this->reportDir.'", please check the permission.');
                $this->log->info ('Cancel generate Report.');
//                throw new Exception('Could not create Report directory "'.$this->reportDir.'", please check the permission.');
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
        $executionTime        = $this->getMicroTime() - $startTime;
        $query                = preg_replace('/ +/', ' ', $queryCommand);        
        $this->queryHistory[] = compact('query', 'totalRecords', 'executionTime');
        $f = fopen('new_billing_peformance.history', file_exists('new_billing_peformance.history') ? 'a' : 'w');
        fwrite($f, json_encode(compact('query', 'totalRecords', 'executionTime'),192).PHP_EOL.'---------------'.PHP_EOL);
        fclose($f);
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
     * Get all User list from SMS_API_V2.USER
     * 
     * @return  Mixed   2D Array [['USER_ID', 'USER_NAME', 'BILLING_PROFILE_ID', 'BILLING_REPORT_GROUP_ID', 'BILLING_TIERING_GROUP_ID']]
     */
    public function getUserDetail($userId = null, $billingProfile = null) {
        $whereClause = !is_null($userId) || is_null($billingProfile)
                        ? ' WHERE '
                        : '';
        
        $userClause = !is_null($userId)
                        ? ' USER_ID '
                            . is_array($userId)
                                ? ' IN ('.implode(',', $userId ?: ['\'\'']).')'
                                : ' = '.$userId
                        : '';
        
        $billingClause = !is_null($billingProfile)
                            ? !is_null($userId)
                                ? ' AND BILLING_PROFILE_ID IS NULL '
                                : ' BILLING_PROFILE_ID IS NULL '
                            : '';
        
        return $this->query(
                         ' SELECT   USER_ID, USER_NAME, BILLING_PROFILE_ID, BILLING_REPORT_GROUP_ID, BILLING_TIERING_GROUP_ID '
                        .' FROM     '.DB_SMS_API_V2.'.USER '
                        .  $userClause
                        .' ORDER BY BILLING_PROFILE_ID'
                    );
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
        
        $deliveryStatus = $this->query(
                                ' SELECT   ERROR_CODE, STATUS, IS_RECREDITED '
                               .' FROM     '.DB_BILL_U_MESSAGE.'.DELIVERY_STATUS '
                               .  $statusClause
                            );
        
        foreach ($deliveryStatus as &$delivery) {
            $delivery['STATUS'] = !(bool) $delivery['IS_RECREDITED']
                                    ? $delivery['STATUS'] == 'Undelivered'
                                        ? 'UNDELIVERED (CHARGED)'
                                        : 'DELIVERED'
                                    : 'UNDELIVERED (NOT CHARGED)';
        }

        return $deliveryStatus;
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
                         ' SELECT   BILLING_PROFILE_ID, BILLING_TYPE, DESCRIPTION, CREATED_AT, UPDATED_AT'
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
     * Get Operator Dial Prefix form First_Intermedia.OPERATOR_DIAL_PREFIX      <br />
     * 
     * @return  Array   2D Array [['OP_ID', 'PREFIX', 'MIN_LENGTH', 'MAX_LENGTH']]
     */
    public function getOperatorDialPrefix(Array $opId){
        $operator = implode(',', $opId);
        return $this->query(
                         ' SELECT   OP_ID,'
                        .'          SUBSTRING(OP_DIAL_RANGE_LOWER, 1, LOCATE(\'00\', OP_DIAL_RANGE_LOWER) - 1) AS PREFIX,'
                        .'          MIN( LENGTH(OP_DIAL_RANGE_LOWER)) AS MIN_LENGTH,'
                        .'          MAX( LENGTH(OP_DIAL_RANGE_UPPER)) AS MAX_LENGTH'
                        .' FROM     OPERATOR_DIAL_PREFIX'
                        .' WHERE    OP_ID IN ('.$operator.')'
                        .' GROUP BY PREFIX'
                    );
    }
    
    
    /**
     * Get Tiering Base Price Rule
     * 
     * @return  Array   2D Array [['SMS_COUNT_FROM', 'SMS_COUNT_UP_TO', 'PER_SMS_PRICE']]
     */
    public function getTieringDetail($billingProfileId) {
        return $this->query(
                         ' SELECT   SMS_COUNT_FROM, SMS_COUNT_UP_TO, PER_SMS_PRICE'
                        .' FROM     '.DB_BILL_PRICELIST.'.BILLING_PROFILE_TIERING'
                        .' WHERE    BILLING_PROFILE_ID = '.$billingProfileId
                    );
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
                         ' SELECT   NAME, DESCTIPTION, CREATED_AT, UPDATED_AT'
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
        return $this->query(
                         ' SELECT    USER_ID, USER_NAME'
                        .' FROM     '.DB_SMS_API_V2.'.USER'
                        .' WHERE    BILLING_TIERING_GROUP_ID = '.$tieringGroupId
                        ,  self::QUERY_SINGLE_COLUMN
                    );
    }
    
    
    /**
     * Get Tiering Group Monthly SMS Traffic
     * 
     * @param   Int     $tieringGroupId     Tiering Group Id
     * @param   Int     $year               SMS Traffic Year period
     * @param   Int     $month              SMS Traffic Month period
     * @return  Int                         Total Group SMS Traffic
     */
    public function getTieringGroupTraffic(Array &$deliveryStatus, String $tieringGroupId, String $year, String $month) {
        $usersClause    = implode(',', $this->getTieringGroupUserList($tieringGroupId)  ?: ['\'\'']);
        $statusClause   = implode(',', $deliveryStatus                                  ?: ['\'\'']);
        
        return $this->query(
                         ' SELECT   COUNT(USER_ID_NUMBER) '
                        .' FROM     '.DB_SMS_API_V2.'.USER '
                        .' WHERE    USER_ID_NUMBER IN ('.$usersClause.') '
                        .'          AND MESSAGE_STATUS IN ('.$statusClause.') '
                        .'          AND YEAR(SEND_DATETIME)  = '.$year
                        .'          AND MONTH(SEND_DATETIME) = '.$monthr
                        ,  self::QUERY_SINGLE_ROW_AND_COLUMN
                    ) ?: 0;
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
                         ' SELECT   BILLING_REPORT_GROUP_ID, NAME, DESCRIPITON, CREATED_AT, UPDATED_AT'
                        .' FROM     '.DB_BILL_PRICELIST.'.BILLING_REPORT_GROUP'
                        .  $groupClause
                        ,  !is_null($reportGroupId) ?: self::QUERY_SINGLE_ROW
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
                        ,  self::QUERY_SINGLE_COLUMN
                    );
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
     * @return  Array                               2D Array [[                                             <br />
     *                                              'MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT',         <br />
     *                                              'MESSAGE_STATUS', 'DESCRIPTION_CODE', 'SEND_DATETIME',  <br />
     *                                              'SENDER', 'USER_ID', 'MESSAGE_COUNT'                    <br />
     *                                              ]]
     */    
    public function getUserMessageStatus($userId, $startDateTime, $endDateTime, $dataSize, $startIndex) {
        
        //MESSAGE ID                    DESTINATION	MESSAGE CONTENT	ERROR CODE	DESCRIPTION CODE	SEND DATETIME           SENDER      USER ID     MESSAGE COUNT	||  OPERATOR    PRICE
        //5GPI2017-04-11 06:13:29.470	15629689999	Test msg sms	0+0+0+0         DELIVERED               2017-04-09 00:00:00	1rstWAP     PEPTrial                1	||  DEFAULT     315
        $userIdClause = is_array($userId)
                            ? ' in ('.implode(',', $userId).')'
                            : ' = '.$userId;
        
        $messages     = $this->query(
                            ' SELECT    MESSAGE_ID, DESTINATION,  MESSAGE_CONTENT, MESSAGE_STATUS, \'\' AS DESCRIPTION_CODE, SEND_DATETIME, SENDER_ID, USER_ID'
                           .' FROM      '.DB_SMS_API_V2.'.USER_MESSAGE_STATUS'
                           .' WHERE     USER_ID_NUMBER '.$userIdClause
                           .'           AND SEND_DATETIME >  \''.$startDateTime.'\' '
                           .'           AND SEND_DATETIME <= \''.$endDateTime  .'\' '
                           .' LIMIT     '.$startIndex.','.$dataSize
                        );
        
        foreach($messages as &$message) {
            $message['DESCRIPTION_CODE']    = $this->getMessageStatus($message['MESSAGE_STATUS']);
            $message['MESSAGE_COUNT']       = $this->getMessageCount ($message['MESSAGE_CONTENT']);
        }
        
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
        return $this->isGsm7bit($message, $messageLength)
                    ? $messageLength <= self::GSM_7BIT_SINGLE_SMS
                        ? 1
                        : ceil( $messageLength / self::GSM_7BIT_MULTIPLE_SMS )
                    : $messageLength <= self::UNICODE_SINGLE_SMS
                        ? 1
                        : ceil( $messageLength / self::UNICODE_MULTIPLE_SMS );
    }
    
    
    /**
     * Get Message Status Description by given ERROR_CODE
     * 
     * @param   String  $errorCode      Error_Code | Message_Status
     * @return  String                  Description of ERROR_CODE
     */
    private function getMessageStatus($errorCode) {
        $key =  array_search(
                    $errorCode,
                    array_column(
                        $this->deliveryStatus, 
                        'ERROR_CODE'
                    )
                );
        
        return $key !== false
                    ? $this->deliveryStatus[$key]['STATUS']
                    : self::DEFAULT_DELIVERY_STATUS;
    }
    
    
    /**
     * Check if the message was Gsm_7bit or Unicode encoded
     * 
     * @param   String  $message        Message content
     * @param   Int     $messageLength  Count of Message characters
     * @return  String                  SMS_TYPE_UNICODE or SMS_TYPE_GSM_7BIT
     */
    private function isGsm7bit($message, $messageLength) {
        for( $i = 0; $i < $messageLength; $i++ ) {
            if( strpos(self::GSM_7BIT_CHARS, $message[$i]) == false 
                && $message[$i]!='\\' ) {
                return self::SMS_TYPE_UNICODE;
            }
        }
        return self::SMS_TYPE_GSM_7BIT;
    }
    
    
    /**
     * Parsing the Destination number to get it's own Operator Name
     * 
     * @param   String  $destination    Destination number wich will be parsing
     * @param   Array   $operators      2D Array of Operator                                <br />
     *                                  could be get from getOperatorDialPrefix()           <br />
     *                                  [['OP_ID', 'PREFIX', 'MIN_LENGTH', 'MAX_LENGTH']]   <br />
     * @return  String                  Operator Name or self::DEFAULT_OPERATOR
     */
    private function getDestinationOperator ($destination, &$operators) {
        foreach($operators as &$operator) {
            if( preg_match(
                    '/(?=^'.$operator['PREFIX'].')'
                    .'(\d{'.$operator['MIN_LENGTH'].','.$operator['MAX_LENGTH'].'})/', 
                    $destination
                )) {
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
                    : $this->assignTieringPrice ($messages, $rules);
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
    private function assignTieringPrice(Array &$messages, Array $rules) {
        array_walk(
            $messages,
            function(&$message) use ($price, $operator) {
                $message['OPERATOR'] = self::DEFAULT_OPERATOR;
                $message['PRICE']    = current($rules)['PER_SMS_PRICE'];
            }
        );
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
     *                              [['OP_ID', 'PREFIX', 'MIN_LENGTH', 'MAX_LENGTH']]
     */
    private function assignOperatorPrice(&$messages, &$rules, &$operators) {
        foreach($messages as &$message) {
            
            $message['OPERATOR'] = $this->getDestinationOperator($message['DESTINATION'], $operators);
            
            /**
             * Find the operator index on the pricing list
             * then take the index 
             */
            $operatorIndex       =  array_search(
                                        $message['OPERATOR'],
                                        array_column(
                                            $rules,
                                            'OP_ID'
                                        )
                                    );
            
            $message['PRICE']    = $operatorIndex === false
                                    ? $rules[$operatorIndex]['PER_SMS_PRICE']
                                    : 0;
        }
    }
        
    
    /**
     * Load last message send date time for every user
     * 
     * @param   String  $dir        directory path for specific report period
     * @return  Array               Array of user's last message send date time
     *                              ['USER_ID' => 'SEND_DATETIME']
     */
    public function loadLastMessageDate($userId = null) {
        $cache = $this->loadCache(self::CACHE_LAST_DATE);
        if(!is_null($userId)) {
            $key   = !empty($cache)
                        ? array_search($userId,array_column($cache, 'USER_ID'))
                        : false;
            
            $cache = $key === false
                        ? ['USER_ID' => $userId, 'SEND_DATETIME' => $this->firstDateOfMonth]
                        : $cache[$key];
        }
        
        return $cache;
    }
    
    
    /**
     * Save last message send date time for every user
     * 
     * @param   Array   $data       List of all user messages last send date time 
     *                              [['USER_ID', 'SEND_DATETIME']]
     * @param   String  $dir        directory path for specific report period
     * @return  Bool                Save status
     */
    public function saveLastMessageDate(Array $data) {
        $list = array_unique(
                    array_merge(
                        $data, 
                        $this->loadLastMessageDate()
                    )
                );
        return $this->saveCache($fileName, json_encode($list));
    }
        

    /**
     * 
     * @param type $year
     * @param type $month
     * @return type
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
            $cache = $key !== false
                        ? $cache[$key]
                        : null;
        }
        
        return $cache;
    }
    
    
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
     * @return  Bool        Save status
     */
    private function saveCache($cacheName, &$contents) {
        $fileName = $this->reportDir.'/cache/'.$cacheName;
        try {
            if(!is_dir(dirname($fileName)) && @mkdir(dirname($fileName)) ) {
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
    
    
    private function createReport($fileName, $awaitingDr = false) {
        $reportAwaiting = $this->reportDir.'/'. self::DIR_AWAITING_DR_REPORT;
        $reportFinal    = $this->reportDir.'/'. self::DIR_FINAL_REPORT;
        is_dir($reportFinal) ?: @mkdir($reportFinal);
        is_dir($reportAwaiting) ?: @mkdir($reportAwaiting);
        $this->reportFileHandler = WriterFactory::create(Type::XLSX);
        $this->reportFileHandler->openToFile($reportFinal.'/'. $fileName.'.xlsx');
        
        if($awaitingDr) {
        }
    }

    
//    private function copyFinalStatusReport($name, $newFixPrice = null) {
//        $finalStatusReport = ReaderFactory::create(Type::XLSX);
//        $finalStatusReport->open($this->reportDir.'/'.$name);
//        foreach ($finalStatusReport->getSheetIterator() as $sheetIndex => $sheet) {
//            // Add sheets in the new file, as you read new sheets in the existing one
//            if ($sheetIndex !== 1) {
//                $writer->addNewSheetAndMakeItCurrent();
//            }
//
//            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
//                $songTitle = $row[0];
//                $artist = $row[1];
//
//                // Change the album name for "Yellow Submarine"
//                if ($songTitle === 'Yellow Submarine') {
//                    $row[2] = 'The White Album';
//                }
//
//                // skip Bob Marley's songs
//                if ($artist === 'Bob Marley') {
//                    continue;
//                }
//
//                // write the edited row to the new file
//                $writer->addRow($row);
//
//                // insert new song at the right position, between the 3rd and 4th rows
//                if ($rowIndex === 3) {
//                    $writer->addRow(['Hotel California', 'The Eagles', 'Hotel California', 1976]);
//                }
//            }
//        }            
//        $finalStatusReport->close();
//        
//    }
    
    
    private function saveReportFile() {
        $this->reportFileHandler->close();
    }
    
    
    private function getReportHeader($userId) {
        $userIds = is_array($userId) ? implode(', ', $userId) : $userId;
        return [
        /* 1  */    ['Last Updated Report',             date('l, d F Y \a\t h:i A')],
        /* 2  */    ['USER NAME',                       $userIds],
        /* 3  */    ['DELIVERED SMS',                   0],
        /* 4  */    ['UNDELIVERED SMS (CHARGED)',       0],
        /* 5  */    ['UNDELIVERED SMS (NOT CHARGED)',   0],
        /* 6  */    ['TOTAL SMS',                       0],
        /* 7  */    ['TOTAL SMS CHARGED',               0],
        /* 8  */    [''],
        /* 9  */    ['TOTAL PRICE',                     0],
        /* 10 */    [''],
        /* 11 */    [''],
        /* 12 */    ['MESSAGE ID','DESTINATION','MESSAGE CONTENT','ERROR CODE','DESCRIPTION CODE', 'SEND DATETIME', 'SENDER', 'USER ID','MESSAGE COUNT','OPERATOR','PRICE'],
        ];
    }
    

    private function insertIntoReportFile(Array &$messages) {
        $this->reportFileHandler->addRows($messages);
    }
    
    
    private function removeUserFromList(&$removeUser, &$existingUser) {
        foreach($removeUser as $rUser) {
            $index = array_search(
                        $rUser,
                        array_column($existingUser, 'USER_ID')
                    );
            
            if($key !== false){
                unset($existingUser[$index]);
            }
        }
    }
    
    
    
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
        
        die(json_encode([
                'var'    => $var, 
                'query'  => $this->queryHistory,
                'memory' => (memory_get_peak_usage(1) /1024/1024).' MB',
            ], 192).PHP_EOL);
    }
    
    
    public function generate() {
                
        if($this->lastFinalStatusDate !== false) {
            
            $users                = $this->getUserDetail();
            $chargedErrorCode     = $this->getDeliveryStatus(self::SMS_STATUS_CHARGED);
            $prevBillingProfileId = null;
            $excludedUser         = [];
            
            /**
             * Get all cache or get from database if not exist
             */
            // $lastSendTime     = $this->loadLastMessageDate    ();
            // $reportGroups     = $this->loadReportGroupCache   ();
            // $billingProfiles  = $this->loadBillingProfileCache();
            
            
            foreach($users as &$user) {
                $fileName              = $user['USER_NAME'];
                $userName              = $user['USER_NAME'];
                $userId                = $user['USER_ID'];
                $userBillingProfileId  = $user['BILLING_PROFILE_ID'];
                $userTieringGroupId    = $user['BILLING_TIERING_GROUP_ID'];
                $userTieringGroup      = null;
                $userReportGroupId     = $user['BILLING_REPORT_GROUP_ID'];
                $userReportGroup       = null;
                
                $this->log->info('Start generate report for user '.$userName);
                
                $this->log->debug('Load last message date');
                $userLastSendDate      = $this->loadLastMessageDate($userId)['SEND_DATETIME'];
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
                
                $this->log->debug('check user billing detail');
                if(is_null($userBillingProfileId)) {
                    $this->log->warn('User '.$userName.' was assigned to Billing Profile'.$userBillingProfileId.' but not found on '.DB_BILL_PRICELIST.'.BILLING_PROFILE');
                    $this->log->info('Skip generate report for user '.$userName);
                    continue;
                }
                
                $this->log->debug('get user billing information');
                $userBillingProfile    = $this->loadBillingProfileCache($userBillingProfileId);
                /* =======================================
                 *  End Of Get User billing information
                 * ======================================= */

                
                
                /*=======================================
                 * Get Report Group information
                 *=======================================*/
                $this->log->debug('check user report group');
                if(!empty($userReportGroupId)) {
                    $this->log->debug('get user report group information');
                    $userReportGroup = $this->loadReportGroupCache($userReportGroupId);
                    if(is_null($userReportGroup)) {
                        $this->log->warn('User '.$userName.' was assigned to Billing Profile "'.$userReportGroupId.'" but not found on '.DB_BILL_PRICELIST.'.BILLING_REPORT_GROUP');
                        $this->log->info('Skip generate report for user '.$userName);
                        continue;
                    }
                    
                    $fileName     = $userReportGroup['NAME'];
                    $this->log->debug('get list of user on report group '.$filname);
                    $userBrother  = $this->getReportGroupUserList($userReportGroupId);                    
                    $userId       = array_merge([$userId], $userBrother);
                    $excludedUser = array_merge($excludedUser, $userBrother);
                }
                /* =======================================
                 *  End of Get report Group information
                 * ======================================= */
                
                
                /* =======================================
                 *  Validate Tiering Group information
                 * ======================================= */
//                $this->log->debug('check user '.$filname);
//                if(!empty($userTieringGroupId) && $userBillingProfile['BILLING_TYPE'] == self::BILLING_TIERING_BASE) {
//                    $userReportGroup = $this->loadReportGroupCache($userReportGroupId);
//                    if(is_null($userReportGroup)) {
//                        $this->log->warn('User '.$userName.' was assigned to Report group "'.$userReportGroupId.'" but not found on '.DB_BILL_U_MESSAGE.'BILL_PRICELIST');
//                        $this->log->info('Skip generate report for user '.$userName);
//                        continue;
//                    }
//                }
                /* =======================================
                 *  End of Validate Tiering Group information
                 * ======================================= */
                
                
                /* =============================================================
                 *  Start get User messages and insert into report file
                 * ============================================================= */
                $this->log->debug('start create report file');
                $messages     = $this->getUserMessageStatus($userId, $userLastSendDate, $this->lastFinalStatusDate, REPORT_PER_BATCH_SIZE, $counter +1);
                $reportHeader = $this->getReportHeader($userId);
                    
                $this->log->debug('memory: '.(memory_get_peak_usage(1) /1024/1024).' MB');
                if(!empty($messages)) {
                    $this->createReport($fileName);
                    if($userBillingProfile['BILLING_TYPE'] == self::BILLING_OPERATOR_BASE) {
                        $this->insertIntoReportFile($reportHeader);
                        do {
                        $this->log->debug('memory: '.(memory_get_peak_usage(1) /1024/1024).' MB');
                            $this->assignMessagePrice(self::BILLING_OPERATOR_BASE, $messages, $userBillingProfile['PRICING']);
                            $this->insertIntoReportFile($messages);
                            $lastSendTime['USER_ID'] = end($messages)['SEND_DATETIME'];
                            $counter                += REPORT_PER_BATCH_SIZE;
                            $messages = $this->getUserMessageStatus($userId, $userLastSendDate, $this->lastFinalStatusDate, REPORT_PER_BATCH_SIZE, $counter +1);
                            $this->log->debug('memory: '.(memory_get_peak_usage(1) /1024/1024).' MB');
                        } while(!empty($messages));
                    }
                    else {
                    }
                    $this->saveReportFile();
                }
                else {
                    $this->log->info('Skip generate report for '.$fileName.', No new messages found.');
                }
                
                /* =============================================================
                 *  End Of Start get User messages and insert into report file
                 * =============================================================*/

            }
$this->debug($messages);
            
        }
        
    }
//    
//    
//    public function download($userId, $year, $month, $awaitingDr = false) {
//        $newFilePath = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . "{$year}-{$month}/{$nameFile}";
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/xlsx');
//        header('Content-Disposition: attachment; filename=' . basename($newFilePath));
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        header('Content-Length: ' . filesize($newFilePath));
//        readfile($newFilePath);
//    }
//    
        
    
    
    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    public function getProfileClient($clientID) {
//        try {
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
//            $query = "SELECT CLIENT_ID, BILLED_SMS, UNKNOWN, PENDING, ";
//            $query .= "UNDELIVERED, DELIVERED, DELIVERED_DESC, TOTAL_SMS, ";
//            $query .= "TOTAL_CHARGE, PROVIDER, PROVIDER_DESC ";
//            $query .= "FROM BILLING_OPTIONS ";
//            $query .= "WHERE CLIENT_ID = '" . $clientID . "'";
//
//            $list = $db->query($query)->fetch(PDO::FETCH_ASSOC);
//            return $list;
//        } catch (Throwable $e) {
//            $this->log->error("$e");
//            throw new Exception("Query failed get Profile");
//        }
//    }
//    
//    public function getBillingClient() {
//        try {
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
//              $query = "SELECT CLIENT_ID ";
//            $query .= "FROM SMS_API_V21.CLIENT A";
////            $query .= "FROM SMS_API_V21.CLIENT A";
//            
//            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
//            
//            return $list;
//            
//        } catch (Throwable $e) {
//            $this->log->error("$e");
//	    throw new Exception("Query failed get Profile");
//        }
//    }          
//    
//    public function getDataReport($userId, $month, $year, $lastUpdated) {
//        try {
//            $lastUpdated = $lastUpdated === false || $lastUpdated == '' ? date("Y-m-01", strtotime("now")) : date("Y-m-d",  strtotime($lastUpdated));
//            $now         = date("Y-m-d", strtotime("now"));
//            $now         = date('m', strtotime($lastUpdated)) == date('m', strtotime($now)) ? $now : date("Y-m-t", strtotime($lastUpdated));
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
//            
//            
//            // HARDCODE
//            // ===================
//            // $month = '03';
//            // $year  = '2016';
//            
//
//            /**
//             * =================================================================
//             *              NEW QUERY WITH PRICE
//             * =================================================================
//             */
////            $query = "SELECT 
////                        MESSAGE_ID,
////                        DESTINATION,
////                        MESSAGE_CONTENT,
////                        MESSAGE_STATUS,
////                        CASE X.IS_RECREDITED
////                            WHEN 0 THEN 
////                                CASE X.STATUS 
////                                    WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
////                                    ELSE 'DELIVERED'
////                                END
////                            WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
////                        END AS DESCRIPTION_CODE,                        
////                        SEND_DATETIME,
////                        SENDER,
////                        USER_ID,
////                        MESSAGE_COUNT,
////                        IF(X.IS_RECREDITED = 0, IF(X.STATUS <>  'Undelivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
////                        IF(X.IS_RECREDITED = 1, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED_UNCHARGED,
////                        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED
////                        FROM
////                    (SELECT 
////                        B.MESSAGE_ID,
////                        B.DESTINATION,
////                        B.SEND_DATETIME,
////                        B.MESSAGE_CONTENT, 
////                        B.MESSAGE_STATUS,
////                        B.SENDER,
////                        B.USER_ID,
////                        IF(LENGTH(B.MESSAGE_CONTENT) <= 160,
////                            1,
////                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)) AS MESSAGE_COUNT,
////                            D.IS_RECREDITED,
////                            D.STATUS,
////                        B.USER_ID_NUMBER
////                    FROM SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (`IDX_SENT_TIMESTAMP`) 
////                    INNER JOIN BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
////                    WHERE
////                         MONTH(B.SEND_DATETIME) = '$month' AND YEAR(B.SEND_DATETIME) = '$year'
////                         AND (B.SEND_DATETIME > '$lastUpdated 23:59:59' AND B.SEND_DATETIME <= '$now 23:59:59')) AS X
////                    WHERE 
////                            X.IS_RECREDITED IN ('0','1') AND X.USER_ID_NUMBER = '$userId'  ORDER BY SEND_DATETIME ASC";
//
//            
//            
//            
//            $query = "SELECT 
//                            MESSAGE_ID,
//                            DESTINATION,
//                            MESSAGE_CONTENT,
//                            MESSAGE_STATUS,
//                            CASE X.IS_RECREDITED
//                                    WHEN 0 THEN
//                                            CASE X.STATUS
//                                                    WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
//                                                    ELSE 'DELIVERED'
//                                            END
//                                    WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
//                            END AS DESCRIPTION_CODE,
//                            SEND_DATETIME,
//                            SENDER,
//                            USER_ID,
//                            MESSAGE_COUNT,
//                            IFNULL(
//                                OP_ID, 
//                                'DEFAULT'
//                            ) AS OPERATOR,
//                            CASE X.IS_RECREDITED
//                                    WHEN 1 THEN '0'
//                                    ELSE IFNULL(    PER_SMS_PRICE, 
//                                                        (   
//                                                                SELECT      BPMX.PER_SMS_PRICE 
//                                                                FROM        BILL_PRICELIST.BILLING_PROFILE_MAP AS BPMX, 
//                                                                            SMS_API_V21.USER AS USRX
//                                                                WHERE       BPMX.OP_ID = 'DEFAULT' 
//                                                                            AND BPMX.BILLING_PROFILE_ID = USRX.BILLING_PROFILE_ID
//                                                                GROUP BY    PER_SMS_PRICE
//                                                        )
//                                                ) * MESSAGE_COUNT
//                            END AS PRICE,
//                            IF(
//                                X.IS_RECREDITED = 0,
//                                IF(
//                                    X.STATUS <> 'Undelivered',
//                                    MESSAGE_COUNT,
//                                    0
//                                ),
//                                0
//                            ) AS DELIVERED,
//                            IF(
//                                X.IS_RECREDITED = 1,
//                                IF(
//                                    X.STATUS = 'Undelivered',
//                                    MESSAGE_COUNT,
//                                    0
//                                ),
//                                0
//                            ) AS UNDELIVERED_UNCHARGED,
//                            IF(
//                                X.IS_RECREDITED = 0,
//                                IF(
//                                    X.STATUS = 'Undelivered',
//                                    MESSAGE_COUNT,
//                                    0
//                                ),
//                                0
//                            ) AS UNDELIVERED
//                        FROM 
//                            (  
//                                    SELECT 
//                                            B.MESSAGE_ID,
//                                            B.DESTINATION,
//                                            B.MESSAGE_CONTENT,
//                                            B.MESSAGE_STATUS,
//                                            B.SEND_DATETIME,
//                                            B.SENDER,
//                                            B.USER_ID,
//                                            IF(
//                                                    LENGTH(B.MESSAGE_CONTENT) <= 160, 1, 
//                                                    CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)
//                                            ) AS MESSAGE_COUNT,
//                                            D.IS_RECREDITED,
//                                            D.STATUS,
//                                            B.USER_ID_NUMBER
//                                    FROM 
//                                        SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (IDX_SENT_TIMESTAMP)
//                                    INNER JOIN 
//                                        BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
//                                    WHERE   (
//                                                    MONTH(SEND_DATETIME) = '$month'
//                                                    AND YEAR(SEND_DATETIME) = '$year'
//                                            )
//                                            AND (
//                                                    SEND_DATETIME > '$lastUpdated 23:59:59' 
//                                                    AND SEND_DATETIME <= '$now 23:59:59'
//                                            )
//                            ) AS X
//                            LEFT JOIN ( 
//                                    SELECT 
//                                            IFNULL(
//                                                    SUBSTRING(OP_DIAL_RANGE_LOWER, 1, LOCATE('00', OP_DIAL_RANGE_LOWER) - 1),
//                                                    (
//                                                            SELECT  ODP2.OP_ID 
//                                                            FROM    First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP2 
//                                                            WHERE   ODP2.OP_ID = 'DEFAULT'
//                                                    )
//                                            ) AS DESTINATION_PREFIX,
//                                            IF( ODP.OP_ID IN (
//                                                            SELECT  BPM2.OP_ID 
//                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM2 
//                                                            WHERE   BPM2.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
//                                                    ),
//                                                    ODP.OP_ID,
//                                                    'DEFAULT'
//                                            ) AS OP_ID,
//                                            IF( ODP.OP_ID IN (
//                                                            SELECT  BPM3.OP_ID 
//                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM3 
//                                                            WHERE   BPM3.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
//                                                    ),
//                                                    (
//                                                            SELECT  BPM4.PER_SMS_PRICE 
//                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM4 
//                                                            WHERE   BPM4.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
//                                                                    AND BPM4.OP_ID = ODP.OP_ID
//                                                    ),
//                                                    (
//                                                            SELECT  BPM5.PER_SMS_PRICE 
//                                                            FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM5
//                                                            WHERE   BPM5.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
//                                                                    AND BPM5.OP_ID = 'DEFAULT'
//                                                    )
//                                            ) AS PER_SMS_PRICE
//                                    FROM
//                                            First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP, 
//                                            BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM, 
//                                            SMS_API_V21.USER AS USR
//                                    WHERE
//                                            USR.USER_ID = '$userId'
//                                            AND BPM.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
//                                    GROUP BY DESTINATION_PREFIX
//                        ) AS SMS_PRICE 
//                                ON  SUBSTRING(DESTINATION, 1, LENGTH(DESTINATION_PREFIX)) = DESTINATION_PREFIX 
//                                AND DESTINATION_PREFIX <> '' 
//                        WHERE
//                                X.IS_RECREDITED IN ('0' , '1') 
//                                AND X.USER_ID_NUMBER = '$userId'
//                        GROUP BY X.MESSAGE_ID
//                        ORDER BY SEND_DATETIME ASC
//            ";
//
//            
//            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
//            
//            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
//            //$this->logger->info("query = \n$query");
//            return $list;
//        } 
//        catch (Throwable $e) {
//            $this->log->error("$e");
//            throw new Exception("Query failed get Data report: " . $e->getMessage());
//        }
//    }
//    
//    public function getDataCronReport($userId, $month = false, $year = false, $lastUpdateDate = false, $lastMonth=false) {
//        try {
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
//            
//            $month     = $month === false ? date("m") : $month;
//            $year      = $year  === false ? date("Y") : $year;
//            $startDate = $lastUpdateDate === false || $lastUpdateDate == '' ? date("Y-m-01", strtotime("now")) : date('Y-m-d', strtotime($lastUpdateDate));
//            
//            
//            // HARDCODE
//            // ===================
//            // $month     = '04';
//            // $year      = '2017';
//            // $startDate = '2017-04-01';
//
//            $intDate   = (int)date('d') -3;
//            if($lastMonth !== false) {
//                $lastDate = date('Y-m-t', strtotime("$lastUpdateDate"));
//                $endDate  = $lastUpdateDate <= $lastDate 
//                              ? $lastDate 
//                              : date('Y-m-d', strtotime("$lastUpdateDate $intDate days"));
//            }
//            else {
//                $endDate  = date("Y-m-d", strtotime("-2 days"));
//            }
//
//            
//            /**
//             * =================================================================
//             *              OLD QUERY WITHOUT PRICE
//             * =================================================================
//             */
//            //$this->logger->info("$userId| LastUpdate = $lastUpdateDate| start = $startDate ==> end = $endDate");
////            $query = "SELECT 
////                        MESSAGE_ID,
////                        DESTINATION,
////                        MESSAGE_CONTENT,
////                        MESSAGE_STATUS,
////                        CASE X.IS_RECREDITED
////                            WHEN 0 THEN 
////                                CASE X.STATUS 
////                                    WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
////                                    ELSE 'DELIVERED'
////                                END
////                            WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
////                        END AS DESCRIPTION_CODE,
////                        SEND_DATETIME,
////                        SENDER,
////                        USER_ID,
////                        MESSAGE_COUNT,
////                        IF(X.IS_RECREDITED = 0, IF(X.STATUS <>  'Undelivered' , MESSAGE_COUNT, 0), 0) AS DELIVERED,
////                        IF(X.IS_RECREDITED = 1, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED_UNCHARGED,
////                        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED
////                        FROM
////                    (SELECT 
////                        B.MESSAGE_ID,
////                        B.DESTINATION,
////                        B.MESSAGE_CONTENT, B.MESSAGE_STATUS,
////                        B.SEND_DATETIME,
////                        B.SENDER,
////                        B.USER_ID,
////                        IF(LENGTH(B.MESSAGE_CONTENT) <= 160,
////                            1,
////                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)) AS MESSAGE_COUNT,
////                            D.IS_RECREDITED,
////                            D.STATUS,
////                        B.USER_ID_NUMBER
////                    FROM SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (`IDX_SENT_TIMESTAMP`) 
////                    INNER JOIN BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
////                    WHERE
////                        (MONTH(SEND_DATETIME) = '$month' AND YEAR(SEND_DATETIME) = '$year') AND
////                        (SEND_DATETIME >= '$startDate 00:00:00' AND SEND_DATETIME < '$endDate 00:00:00')) AS X
////                    WHERE 
////                            X.IS_RECREDITED IN ('0','1') AND X.USER_ID = '$userId' ORDER BY SEND_DATETIME ASC";
//
//            
//            
//            /**
//             * =================================================================
//             *              NEW QUERY WITH PRICE
//             * =================================================================
//             */
//            $query = "SELECT 
//                            MESSAGE_ID,
//                            DESTINATION,
//                            MESSAGE_CONTENT,
//                            MESSAGE_STATUS,
//                            CASE X.IS_RECREDITED
//                                WHEN 0 THEN
//                                    CASE X.STATUS
//                                        WHEN 'Undelivered' THEN 'UNDELIVERED (CHARGED)'
//                                        ELSE 'DELIVERED'
//                                    END
//                                WHEN 1 THEN 'UNDELIVERED (NOT CHARGED)'
//                            END AS DESCRIPTION_CODE,
//                            SEND_DATETIME,
//                            SENDER,
//                            USER_ID,
//                            MESSAGE_COUNT,
//                            IFNULL(
//                                OP_ID, 
//                                'DEFAULT'
//                            ) AS OPERATOR,
//                            CASE X.IS_RECREDITED
//                                    WHEN 1 THEN '0'
//                                    ELSE IFNULL(    PER_SMS_PRICE, 
//                                                        (   
//                                                                SELECT      BPMX.PER_SMS_PRICE 
//                                                                FROM        BILL_PRICELIST.BILLING_PROFILE_MAP AS BPMX, 
//                                                                            SMS_API_V21.USER AS USRX
//                                                                WHERE       BPMX.OP_ID = 'DEFAULT' 
//                                                                            AND BPMX.BILLING_PROFILE_ID = USRX.BILLING_PROFILE_ID
//                                                                GROUP BY    PER_SMS_PRICE
//                                                        )
//                                                ) * MESSAGE_COUNT
//                            END AS PRICE,
//                            IF(
//                                X.IS_RECREDITED = 0,
//                                IF(
//                                    X.STATUS <> 'Undelivered',
//                                    MESSAGE_COUNT,
//                                    0
//                                ),
//                                0
//                            ) AS DELIVERED,
//                            IF(
//                                X.IS_RECREDITED = 1,
//                                IF(
//                                    X.STATUS = 'Undelivered',
//                                    MESSAGE_COUNT,
//                                    0
//                                ),
//                                0
//                            ) AS UNDELIVERED_UNCHARGED,
//                            IF(
//                                X.IS_RECREDITED = 0,
//                                IF(
//                                    X.STATUS = 'Undelivered',
//                                    MESSAGE_COUNT,
//                                    0
//                                ),
//                                0
//                            ) AS UNDELIVERED
//                        FROM (  
//                                SELECT 
//                                        B.MESSAGE_ID,
//                                        B.DESTINATION,
//                                        B.MESSAGE_CONTENT,
//                                        B.MESSAGE_STATUS,
//                                        B.SEND_DATETIME,
//                                        B.SENDER,
//                                        B.USER_ID,
//                                        IF(
//                                            LENGTH(B.MESSAGE_CONTENT) <= 160, 
//                                            1, 
//                                            CEILING(LENGTH(B.MESSAGE_CONTENT) / 153)
//                                        ) AS MESSAGE_COUNT,
//                                        D.IS_RECREDITED,
//                                        D.STATUS,
//                                        B.USER_ID_NUMBER
//                                FROM 
//                                    SMS_API_V21.USER_MESSAGE_STATUS B FORCE INDEX (IDX_SENT_TIMESTAMP)
//                                INNER JOIN 
//                                    BILL_U_MESSAGE.DELIVERY_STATUS D ON B.MESSAGE_STATUS = D.ERROR_CODE
//                                WHERE   (
//                                            MONTH(SEND_DATETIME) = '$month'
//                                            AND YEAR(SEND_DATETIME) = '$year'
//                                        )
//                                        AND (
//                                            SEND_DATETIME >= '$startDate 00:00:00' 
//                                            AND SEND_DATETIME < '$endDate 00:00:00'
//                                        )
//                                ) AS X
//                                LEFT JOIN ( SELECT 
//                                                    IFNULL(
//                                                            SUBSTRING(OP_DIAL_RANGE_LOWER, 1, LOCATE('00', OP_DIAL_RANGE_LOWER) - 1),
//                                                            (
//                                                                SELECT  ODP2.OP_ID 
//                                                                FROM    First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP2 
//                                                                WHERE   ODP2.OP_ID = 'DEFAULT'
//                                                            )
//                                                    ) AS DESTINATION_PREFIX,
//                                                    IF( ODP.OP_ID IN (
//                                                                SELECT  BPM2.OP_ID 
//                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM2 
//                                                                WHERE   BPM2.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
//                                                            ),
//                                                            ODP.OP_ID,
//                                                            'DEFAULT'
//                                                    ) AS OP_ID,
//                                                    IF( ODP.OP_ID IN (
//                                                                SELECT  BPM3.OP_ID 
//                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM3 
//                                                                WHERE   BPM3.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
//                                                            ),
//                                                            (
//                                                                SELECT  BPM4.PER_SMS_PRICE 
//                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM4 
//                                                                WHERE   BPM4.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
//                                                                        AND BPM4.OP_ID = ODP.OP_ID
//                                                            ),
//                                                            (
//                                                                SELECT  BPM5.PER_SMS_PRICE 
//                                                                FROM    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM5
//                                                                WHERE   BPM5.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID 
//                                                                        AND BPM5.OP_ID = 'DEFAULT'
//                                                            )
//                                                    ) AS PER_SMS_PRICE
//                                            FROM
//                                                    First_Intermedia.OPERATOR_DIAL_PREFIX AS ODP, 
//                                                    BILL_PRICELIST.BILLING_PROFILE_MAP AS BPM, 
//                                                    SMS_API_V21.USER AS USR
//                                            WHERE
//                                                    USR.USER_NAME = '$userId'
//                                                    AND BPM.BILLING_PROFILE_ID = USR.BILLING_PROFILE_ID
//                                            GROUP BY DESTINATION_PREFIX
//                                ) AS SMS_PRICE 
//                                        ON  SUBSTRING(DESTINATION, 1, LENGTH(DESTINATION_PREFIX)) = DESTINATION_PREFIX 
//                                        AND DESTINATION_PREFIX <> '' 
//                        WHERE
//                                X.IS_RECREDITED IN ('0' , '1') 
//                                AND X.USER_ID = '$userId'
//                        GROUP BY X.MESSAGE_ID
//                        ORDER BY SEND_DATETIME ASC
//            ";
//            
//            
////            if($userId == 'PEPTrial'){
////                die($query);
////            }
//            
//            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
//            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
//                        
//            return $list;
//        } 
//        catch (Throwable $e) {
//            $this->log->error($e->getMessage());
//            throw new Exception("Query failed get Data report: " . $e->getMessage());
//        }
//    }
//    
//
//    
//    public function getUser($clientID) {
//        try {
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
//
//            $query = "SELECT USER_NAME FROM USER WHERE CLIENT_ID = '" . $clientID . "' AND ACTIVE = TRUE ";
//            //echo "$query\n";
//            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
//
//            return $list;
//        } catch (Throwable $e) {
//            $this->log->error("getUser errpr: ".$e->getMessage());
//            throw new Exception("Query failed get User");
//        }
//    }
//    
//
//    public function getHeader($userId, $billedSMS, $errorCode, $deliveredDesc) {
//        try {
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
//
//            $query = "SELECT C.MESSAGE_STATUS, sum(C.MESSAGE_COUNT) AS
//                        MESSAGE COUNT FROM (SELECT B.DESTINATION, ";
//            if ($errorCode == false) {
//                if ($deliveredDesc != "") {
//                    $query .= " CASE D.STATUS ";
//                    $pieces = explode(";", $deliveredDesc);
//                    foreach ($pieces as $value) {
//                        $query .= "WHEN '" . $value . "' THEN 'Delivered' ";
//                    }
//
//                    $query .= " else D. STATUS end as MESSAGE_STATUS, ";
//                } else {
//                    $query .= " D. STATUS AS MESSAGE_STATUS, ";
//                }
//            } else {
//                $query .= " B.MESSAGE_STATUS, ";
//            }
//
//
//            $query .= " B.SEND_DATETIME, B.SENDER,
//                        B.USER_ID,
//
//                        if(length(B.MESSAGE_CONTENT)<=160,1,ceiling(length(B.MESSAGE_CONTENT)/153))
//                        as MESSAGE_COUNT ";
//            $query .= " FROM SMS_API_V21.USER_MESSAGE_STATUS B,
//                        BILL_U_MESSAGE.DELIVERY_STATUS D "
//                    . " WHERE B.MESSAGE_STATUS = D.ERROR_CODE ";
//            $query .= " AND D.IS_RECREDITED in (";
//
//            if ($billedSMS = 1 || $billedSMS = 3) {
//                $query .= "'0'";
//                if ($billedSMS = 3) {
//                    $query .= ",";
//                }
//            }
//
//            if ($billedSMS = 2 || $billedSMS = 3) {
//                $query .= "'1'";
//            }
//
//            $query .= ") AND B.USER_ID = '" . $userId . "') C GROUP BY C.MESSAGE_STATUS";
//
//            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
//
//            return $list;
//        } catch (Throwable $e) {
//            $this->log->error("getHeader error: ".$e->getMessage());
//            //echo $e;
//            throw new Exception("Query failed get Header");
//        }
//    }
//    
//
//    public function getHeaderProvider($userId, $billedSMS) {
//        try {
//            $db = SmsApiAdmin::getDB(SmsApiAdmin::DB_ALL);
//
//            $query = "SELECT C.DESTINATION, sum(C.MESSAGE_COUNT) AS MESSAGE COUNT FROM "
//                    . "(SELECT CASE SUBSTRING(B.DESTINATION,1,5)"
//                    . "  WHEN '62811' THEN 'TSEL'"
//                    . "  WHEN '62812' THEN 'TSEL'"
//                    . "  WHEN '62813' THEN 'TSEL'"
//                    . "  WHEN '62821' THEN 'TSEL'"
//                    . "  WHEN '62822' THEN 'TSEL'"
//                    . "  WHEN '62823' THEN 'TSEL'"
//                    . "  WHEN '62851' THEN 'TSEL'"
//                    . "  WHEN '62852' THEN 'TSEL'"
//                    . "  WHEN '62853' THEN 'TSEL'"
//                    . "  ELSE 'NON-TSEL'"
//                    . "  END AS DESTINATION, ";
//
//            $query .= " if(length(B.MESSAGE_CONTENT)<=160,1,ceiling(length(B.MESSAGE_CONTENT)/153)) as MESSAGE_COUNT ";
//            $query .= " FROM SMS_API_V21.USER_MESSAGE_STATUS B, BILL_U_MESSAGE.DELIVERY_STATUS D "
//                    . " WHERE B.MESSAGE_STATUS = D.ERROR_CODE ";
//            $query .= " AND D.IS_RECREDITED in (";
//
//            if ($billedSMS = 1 || $billedSMS = 3) {
//                $query .= "'0'";
//                if ($billedSMS = 3) {
//                    $query .= ",";
//                }
//            }
//
//            if ($billedSMS = 2 || $billedSMS = 3) {
//                $query .= "'1'";
//            }
//
//            $query .= ") AND B.USER_ID = '" . $userId . "') C "
//                   . "GROUP BY C.DESTINATION ORDER BY C.DESTINATION DESC";
//
//            $list = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
//
//            return $list;
//        } catch (Throwable $e) {
//            $this->log->error("getHeaderProvider error: ".$e->getMessage());
//            throw new Exception("Query failed get Header Provider");
//        }
//    }
//
//    
    
}
