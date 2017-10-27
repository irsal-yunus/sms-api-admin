<?php

use PHPUnit\Framework\TestCase;

require_once dirname(dirname(dirname(__DIR__))).'/src/init.d/init.php';
require_once dirname(dirname(dirname(__DIR__))).'/src/lib/model/ApiReport.php';
/**
 * ApiReportTest for class Firstwap/SmsApiAdmin/lib/model/ApiReport
 *
 * @author muhammad Rizal
 */
class ApiReportTest extends TestCase
{

    /**
     * @test
     * Test clientTimeZone method
     *
     * @return void
     */
    public function testClientTimeZone()
    {
        $report = new ApiReport("2017", "10", true);

        // parameter is correct format date
        $date = "2017-10-25";
        $this->assertEquals("2017-10-25 07:00:00", $report->clientTimeZone($date));
        //with custom format
        $date = "2017-10-25";
        $this->assertEquals("2017-10-25 07:00", $report->clientTimeZone($date,'Y-m-d H:i'));
        $date = "2017-10-26 08:00:00";
        $this->assertEquals("2017-10-26 15:00:00", $report->clientTimeZone($date));
        // parameter is timestamps
        $date = "1508976000";
        $this->assertEquals("2017-10-26 07:00:00", $report->clientTimeZone($date));
        $date = "";
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $report->clientTimeZone($date));
        $date = null;
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $report->clientTimeZone($date));
    }
    
    /**
     * @test
     * Test serverTimeZone method
     *
     * @return void
     */
    public function testServerTimeZone()
    {
        $report = new ApiReport("2017", "10", true);

        // parameter is correct format date
        $date = "2017-10-25";
        $this->assertEquals("2017-10-24 17:00:00", $report->serverTimeZone($date));
        //with custom format
        $date = "2017-10-25";
        $this->assertEquals("2017-10-24 17:00", $report->serverTimeZone($date,'Y-m-d H:i'));
        $date = "2017-10-26 08:00:00";
        $this->assertEquals("2017-10-26 01:00:00", $report->serverTimeZone($date));
        // parameter is timestamps
        $date = "1508976000";
        $this->assertEquals("2017-10-25 17:00:00", $report->serverTimeZone($date));
        
        $date = "";
        $this->assertEquals(date('Y-m-d H:i:s'), $report->serverTimeZone($date));
        $date = null;
        $this->assertEquals(date('Y-m-d H:i:s'), $report->serverTimeZone($date));
    }

}
