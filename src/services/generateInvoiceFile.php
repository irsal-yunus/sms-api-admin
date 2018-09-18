#!/usr/bin/php -tt
<?php

use Firstwap\SmsApiAdmin\lib\Modules\InvoiceGenerator;

require dirname(dirname(__DIR__)).'/vendor/autoload.php';
\SmsApiAdmin::init();

$generator = new InvoiceGenerator();

echo "-------------------[ START GENERATE INVOICE ".date('d F Y H:i:s').']-------------------'.PHP_EOL;
$generator->generate();
