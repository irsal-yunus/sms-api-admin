<?php
/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */
require_once 'init.d/init.php';
SmsApiAdmin::destroySession();
SmsApiAdmin::redirectUrl('index.php');