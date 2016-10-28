<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

define('SMSAPIADMIN',1);
define('SMSAPIADMIN_CONFIG_DIR', dirname(__FILE__).'/');
define('SMSAPIADMIN_BASE_DIR', dirname(SMSAPIADMIN_CONFIG_DIR).'/');
define('SMSAPIADMIN_LIB_DIR', SMSAPIADMIN_BASE_DIR.'lib/');
define('SMSAPIADMIN_UTILS_DIR', SMSAPIADMIN_BASE_DIR.'utils/');
define('SMSAPIADMIN_UTILS_DOC_DIR', SMSAPIADMIN_UTILS_DIR.'doc/');
define('SMSAPIADMIN_UTILS_TMP_DIR', SMSAPIADMIN_UTILS_DIR.'tmp/');
//define('SMSAPIADMIN_DATA_CACHE_DIR', SMSAPIADMIN_BASE_DIR.'data_cache/');
define('SMSAPIADMIN_TEMPLATE_DIR', SMSAPIADMIN_BASE_DIR.'templates/');
//define('SMSAPIADMIN_TEMPLATE_CACHE_DIR', SMSAPIADMIN_BASE_DIR.'templates_cache/');
define('SMSAPIADMIN_TEMPLATE_COMPILE_DIR', SMSAPIADMIN_BASE_DIR.'templates_compiled/');
//define('SMSAPIADMIN_BASE_URL', 'http://localhost/smsadmin/');
define('SMSAPIADMIN_BASE_URL', 'http://10.32.6.25/sms-api-admin/src/'); // Configured as the url
define('SMSAPIADMIN_SERVICE_URL', SMSAPIADMIN_BASE_URL.'services/');
define('SMSAPIADMIN_SERVER_TIMEZONE', 'UTC');
define('SMSAPIADMIN_SESSION_NAME', 'SmsApiAdmin');
define('SMSAPIADMIN_SYSTEM_USER_NAME', 'system');
define('SMSAPIADMIN_SYSTEM_USER_ID', 0);
define('SMSAPIADMIN_ARCHIEVE_DEFAULT_SUFFIX', '.tar.gz');
define('SMSAPIADMIN_ARCHIEVE_CMD_CREATE', SMSAPIADMIN_UTILS_DIR.'archive.sh {SRC-NAME} {OUT-NAME}');
define('SMSAPIADMIN_ARCHIEVE_CMD_EXTRACT', 'gzip -d {SRC-NAME} | tar -x');
define('SMSAPIADMIN_ARCHIEVE_CSV_DELIMITER', ',');
define('SMSAPIADMIN_ARCHIEVE_CSV_ENCLOSURE', '"');
//define('SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT', '/home/reports/');
//define('SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT', '/home/reports/');
define('SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT',dirname(__DIR__).'/archive/reports/');//'var/www/html/sms-api-admin/src/archive/reports/');
define('SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT' ,dirname(__DIR__).'/archive/reports/');//'var/www/html/sms-api-admin/src/archive/reports/');
define('REF_DB_HOST', '10.32.6.39');
define('REF_DB_USER', 'devteam');
define('REF_DB_PASSWORD', 'devteam'); 
define('REF_DB_NAME', 'SMS_API_V21');
