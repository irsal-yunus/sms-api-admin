#!/usr/bin/php -tt
<?php

/**
 * @author Basri.Y
 *
 * Copyright(c) 2017 1rstWAP. All rights reserved.
 * -----------------------------------------------
 * #18802   2017-06-07  Basri.Y     [SMS Billing Report] Improve Performance & Tiering
 */

require_once dirname(__DIR__).'/init.d/init.php';
require_once dirname(__DIR__).'/lib/model/ApiReport.php';
proc_nice(8);
echo exec('clear');
$log = Logger::getRootLogger();

try {

    $log ->info("Start generating billing report");

    // Generate Last month report
    (new ApiReport(
            date('Y', strtotime('first day of last month')),
            date('m', strtotime('first day of last month')),
            true
        ))->generate();

    // Generate current month report
    (new ApiReport(
            date('Y',strtotime('now')),
            date('m',strtotime('now')),
            true
        ))->generate();

} catch (Throwable $e) {
    $log->error('generateCronReport Error: '.$e->getMessage());
    $log->error('generateCronReport Error: '.$e->getTraceAsString());
    echo 'generateCronReport Error: '.$e->getMessage();
    echo $e->getTraceAsString();
}

