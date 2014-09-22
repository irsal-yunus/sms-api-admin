<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/configs/config.php';
date_default_timezone_set(SMSAPIADMIN_SERVER_TIMEZONE);

require_once SMSAPIADMIN_LIB_DIR.'com/log4php/Logger.php';
require_once SMSAPIADMIN_LIB_DIR.'com/CommandLine.php';
require_once SMSAPIADMIN_LIB_DIR.'core/utils.lib.php';

Logger::configure(SMSAPIADMIN_CONFIG_DIR.'utils-log.ini');
set_exception_handler(array('SmsApiAdminUtility' , 'catchException'));
set_error_handler(array('SmsApiAdminUtility' , 'catchError'));