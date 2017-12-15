<?php

use Firstwap\SmsApiAdmin\Test\TestCase;



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
     * Test parseDatetimeInput method
     *
     * @return void
     */
    public function testParseDatetimeInput(){
        $report = new ApiReport("2017", "10", true);
        
        // Test if the value is incorrect and will return current date time string
        $value = "abc";
        $value = $this->callMethod($report, 'parseDatetimeInput',[$value]);
        $this->assertEquals(date('Y-m-d H:i:s'), $value);
        
        // Test if the value is empty string and will return current date time string
        $value = "";
        $value = $this->callMethod($report, 'parseDatetimeInput',[$value]);
        $this->assertEquals(date('Y-m-d H:i:s'), $value);

        // Test if the value is empty and isServerTimezone is true should return GMT+7 date time string
        $value = "";
        $value = $this->callMethod($report, 'parseDatetimeInput',[$value, true]);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $value);

        //Test the value is timestamp
        $timestamp = "1508976000";
        $value = $this->callMethod($report, 'parseDatetimeInput',[$timestamp]);
        $this->assertEquals("2017-10-26 00:00:00", $value);
    }

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
        $customFormat = 'Y-m-d H:i';
        $this->assertEquals("2017-10-25 07:00", $report->clientTimeZone($date,$customFormat));

        // Test timezone correctoin of 7 hours
        $date = "2017-10-26 08:00:00";
        $this->assertEquals("2017-10-26 15:00:00", $report->clientTimeZone($date));
        
        // Test if the timezone correction works when GMT is a day behind GMT+7
        $date = "2017-10-26 23:00:00";
        $this->assertEquals("2017-10-27 06:00:00", $report->clientTimeZone($date));
        
        // parameter is timestamp
        $date = "1508976000";
        $this->assertEquals("2017-10-26 07:00:00", $report->clientTimeZone($date));
        
        // Test if the current time is returned if the input parameter is an empty string
        $date = "";
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $report->clientTimeZone($date));
        
        // Test if the current time is returned if the input parameter is null
        $date = null;
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $report->clientTimeZone($date));
        
        // Test if the current time is returned if the input parameter is incorrect
        $date = "abc";
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
        
        // Test if the current time is returned if the input parameter is an empty string
        $date = "";
        $this->assertEquals(date('Y-m-d H:i:s'), $report->serverTimeZone($date));
        
        // Test if the current time is returned if the input parameter is null
        $date = null;
        $this->assertEquals(date('Y-m-d H:i:s'), $report->serverTimeZone($date));
        
        // Test if the current time is returned if the input parameter is incorrect
        $date = "abc";
        $this->assertEquals(date('Y-m-d H:i:s'), $report->serverTimeZone($date));
    }

}
