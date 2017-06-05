<?php
/** 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

/**
 * Application Url Configuration
 */
define('SMSAPIADMIN_BASE_URL',      'http://10.32.6.5/sms-api-admin/src/'); // Configured as the url
define('SMSAPIADMIN_SERVICE_URL',   SMSAPIADMIN_BASE_URL.'services/');


/**
 * Directory configuration
 */
define('SMSAPIADMIN_CONFIG_DIR',                dirname(__FILE__).'/');
define('SMSAPIADMIN_BASE_DIR',                  dirname(SMSAPIADMIN_CONFIG_DIR).'/');
define('SMSAPIADMIN_LIB_DIR',                   SMSAPIADMIN_BASE_DIR.'lib/');
define('SMSAPIADMIN_UTILS_DIR',                 SMSAPIADMIN_BASE_DIR.'utils/');
define('SMSAPIADMIN_UTILS_DOC_DIR',             SMSAPIADMIN_UTILS_DIR.'doc/');
define('SMSAPIADMIN_UTILS_TMP_DIR',             SMSAPIADMIN_UTILS_DIR.'tmp/');
define('SMSAPIADMIN_TEMPLATE_DIR',              SMSAPIADMIN_BASE_DIR.'templates/');
define('SMSAPIADMIN_TEMPLATE_COMPILE_DIR',      SMSAPIADMIN_BASE_DIR.'templates_compiled/');
define('SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT',     dirname(__DIR__).'/archive/reports/');
// define('SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT' ,     dirname(__DIR__).'/archive/reports/');


/**
 * Database Configuration
 */
define('REF_DB_HOST',           '10.32.6.71');
define('REF_DB_USER',           'qateam');
define('REF_DB_PASSWORD',       'qateam'); 

define('DB_SMS_API_V2',         'SMS_API_V2');
define('DB_BILL_U_MESSAGE',     'BILL_U_MESSAGE');
define('DB_BILL_PRICELIST',     'BILL_PRICELIST');
define('DB_First_Intermedia',   'First_Intermedia');


/**
 * Utility Configuration
 */
define('SMSAPIADMIN_SERVER_TIMEZONE',            'UTC');
define('SMSAPIADMIN_SESSION_NAME',               'SmsApiAdmin');
define('SMSAPIADMIN_SYSTEM_USER_NAME',           'system');
define('SMSAPIADMIN_SYSTEM_USER_ID',             0);
define('SMSAPIADMIN_ARCHIEVE_DEFAULT_SUFFIX',    '.tar.gz');
define('SMSAPIADMIN_ARCHIEVE_CMD_CREATE',        SMSAPIADMIN_UTILS_DIR.'archive.sh {SRC-NAME} {OUT-NAME}');
define('SMSAPIADMIN_ARCHIEVE_CMD_EXTRACT',       'gzip -d {SRC-NAME} | tar -x');
define('SMSAPIADMIN_ARCHIEVE_CSV_DELIMITER',     ',');
define('SMSAPIADMIN_ARCHIEVE_CSV_ENCLOSURE',     '"');
define('REPORT_PER_BATCH_SIZE',                  1000);