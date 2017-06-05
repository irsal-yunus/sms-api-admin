#!/usr/bin/php -tt
<?php

/**
 * @author Basri Yasin 
 */


proc_nice(8);
echo exec('clear');
$log = Logger::getRootLogger();

try {

    $log ->info("Start generating billing report");
    
    // Generate Last month report
    (new ApiReport(
            date('Y',strtotime('-1 months')), 
            date('Y',strtotime('-1 months'))
        ))->generate();
    
    // Generate current month report
    (new ApiReport(
            date('Y',strtotime('now')), 
            date('m',strtotime('now'))
        ))->generate();
    
} catch (Throwable $e) {
    $log->error('generateCronReport Error: '.$e->getMessage());
    $log->error('generateCronReport Error: '.$e->getTraceAsString());
    echo 'generateCronReport Error: '.$e->getMessage();
    echo $e->getTraceAsString();
}

