<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

require_once dirname(dirname(__FILE__)).'/configs/config.php';
date_default_timezone_set(SMSAPIADMIN_SERVER_TIMEZONE);

require_once SMSAPIADMIN_LIB_DIR.'com/log4php/Logger.php';
require_once SMSAPIADMIN_LIB_DIR.'core/IUser.php';
require_once SMSAPIADMIN_LIB_DIR.'core/ILoginManager.php';
require_once SMSAPIADMIN_LIB_DIR.'core/admin.lib.php';
require_once SMSAPIADMIN_LIB_DIR.'model/ApiBaseModel.php';

SmsApiAdmin::init();
SmsApiAdmin::setServiceMode(SmsApiAdmin::SERVICE_TYPE_JSON);
set_exception_handler(array('SmsApiAdmin' , 'catchException'));
set_error_handler(array('SmsApiAdmin' , 'catchError'));