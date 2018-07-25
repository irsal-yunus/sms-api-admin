<?php

use Firstwap\SmsApiAdmin\Test\TestCase;

require_once dirname(dirname(dirname(__DIR__))) . '/src/lib/model/ApiReport.php';

/**
 * ApiReportTest for class Firstwap/SmsApiAdmin/lib/model/ApiReport
 *
 * @author muhammad Rizal
 */
class ApiReportTest extends TestCase
{

    protected $opertors = [
        [
            "OP_ID" => "DEFAULT",
            "RANGE_LOWER" => "0000",
            "RANGE_UPPER" => "0000000000000"
        ],
        [
            "OP_ID" => "THREE",
            "RANGE_LOWER" => "62895000000",
            "RANGE_UPPER" => "62899999999"
        ],
        [
            "OP_ID" => "THREE",
            "RANGE_LOWER" => "628950000000",
            "RANGE_UPPER" => "628999999999"
        ],
        [
            "OP_ID" => "THREE",
            "RANGE_LOWER" => "6289500000000",
            "RANGE_UPPER" => "6289999999999"
        ],
        [
            "OP_ID" => "THREE",
            "RANGE_LOWER" => "62895000000000",
            "RANGE_UPPER" => "62899999999999"
        ]
    ];

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        if (file_exists('src/archive/reports')) {
            shell_exec("mv src/archive/reports src/archive/reports-bk");
        }
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        if (file_exists('src/archive/reports')) {
            shell_exec("rm -rf src/archive/reports");
        }

        if (file_exists('src/archive/reports-bk')){
            shell_exec("mv src/archive/reports-bk src/archive/reports");
        }
    }

    /**
     * Test getUserBillingGroup method
     *
     * @return void
     */
    public function testGetUserBillingGroupMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getUserBillingGroup(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getUserBillingGroup();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getBilingProfileDetail method
     *
     * @return void
     */
    public function testGetBilingProfileDetailMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getBilingProfileDetail(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getBilingProfileDetail();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getOperatorBaseDetail method
     *
     * @return void
     */
    public function testGetOperatorBaseDetailMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getOperatorBaseDetail(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getOperatorDetail method
     *
     * @return void
     */
    public function testGetOperatorDetailMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getOperatorDetail([1]);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getOperatorDetail();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getUserDetail method
     *
     * @return void
     */
    public function testGetUserDetailMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getUserDetail();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getUserDetail(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getUserDetail([1],1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getGroupLastSendDate method
     *
     * @return void
     */
    public function testGetGroupLastSendDateMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getGroupLastSendDate([1]);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getGroupLastSendDate();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getGroupLastSendDate([]);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getOperatorDialPrefix method
     *
     * @return void
     */
    public function testGetOperatorDialPrefixMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getOperatorDialPrefix([1]);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getOperatorDialPrefix();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getOperatorDialPrefix([]);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getTieringDetail method
     *
     * @return void
     */
    public function testGetTieringDetailMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getTieringDetail(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
        $result = $report->getTieringDetail('');
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getTieringGroupDetail method
     *
     * @return void
     */
    public function testGetTieringGroupDetailMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getTieringGroupDetail(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getTieringGroupDetail();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getTieringGroupUserList method
     *
     * @return void
     */
    public function testGetTieringGroupUserListMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getTieringGroupUserList(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getTieringGroupUserList();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getReportGroupDetail method
     *
     * @return void
     */
    public function testGetReportGroupDetailMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getReportGroupDetail(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getReportGroupDetail();
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getReportGroupUserList method
     *
     * @return void
     */
    public function testGetReportGroupUserListMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getReportGroupUserList(1);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getUserMessageStatus method
     *
     * @return void
     */
    public function testGetUserMessageStatusMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getUserMessageStatus([1], $report->firstDateOfMonth, $report->lastDateOfMonth,1,0);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getUserMessageStatus(1, $report->firstDateOfMonth, $report->lastDateOfMonth,1,0);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getGroupMessageStatus method
     *
     * @return void
     */
    public function testGetGroupMessageStatusMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getGroupMessageStatus([1 => $report->firstDateOfMonth], $report->lastDateOfMonth,1,0);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $result = $report->getGroupMessageStatus([], $report->lastDateOfMonth,1,0);
        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
    }

    /**
     * Test getTieringTraffic method
     *
     * @return void
     */
    public function testGetTieringTrafficMethod()
    {
        $report = new ApiReport("2018", "01", true);

        $result = $report->getTieringTraffic([['USER_ID' => 1]]);
        $this->assertNotNull($result);

        $result = $report->getTieringTraffic(1, true);
        $this->assertNotNull($result);
    }

    /**
     * Test assignMessagePrice method
     *
     * @return void
     */
    public function testAssignMessagePriceMethod()
    {
        $report = new ApiReport("2018", "01", true);
        $operators = $this->opertors;
        $price = [
            [
                "OP_ID" => "DEFAULT",
                "PER_SMS_PRICE" => "200"
            ]
        ];

        $message = [
            [
                'MESSAGE_ID' => "0GPI2018-01-02 00:00:01.000.uvgjE",
                'DESTINATION' => "628190000001",
                'MESSAGE_CONTENT' => "DUMMY TEXT DELETE THIS IF MENGGANGGU WKWKWKW",
                'MESSAGE_STATUS' => "23",
                'DESCRIPTION_CODE' => "",
                'RECEIVE_DATETIME' => "2018-01-02 00:00:01",
                'SEND_DATETIME' => "2018-01-02 00:00:01",
                'SENDER' => "Rachmat",
                'USER_ID' => "rachmat",
            ],
            [
                'MESSAGE_ID' => "0GPI2018-01-02 00:00:01.000.uvgjE",
                'DESTINATION' => "628190000001",
                'MESSAGE_CONTENT' => "DUMMY TEXT DELETE THIS IF MENGGANGGU WKWKWKW",
                'MESSAGE_STATUS' => "0+0+0+0",
                'DESCRIPTION_CODE' => "",
                'RECEIVE_DATETIME' => "2018-01-02 00:00:01",
                'SEND_DATETIME' => "2018-01-02 00:00:01",
                'SENDER' => "Rachmat",
                'USER_ID' => "rachmat",
            ]
        ];

        $this->callMethod($report, 'assignMessagePrice', [ApiReport::BILLING_OPERATOR_BASE, &$message, &$price, &$operators]);
        $this->assertArrayHasKey('OPERATOR', current($message));
        $this->assertArrayHasKey('MESSAGE_COUNT', current($message));
        $this->assertArrayHasKey('PRICE', current($message));
        $this->assertNotEmpty(current($message)['DESCRIPTION_CODE']);
        $this->assertNotEquals("2018-01-02 00:00:01", current($message)['RECEIVE_DATETIME']);
        $this->assertNotEquals("2018-01-02 00:00:01", current($message)['SEND_DATETIME']);

        $message = [
            [
                'MESSAGE_ID' => "0GPI2018-01-02 00:00:01.000.uvgjE",
                'DESTINATION' => "628190000001",
                'MESSAGE_CONTENT' => "DUMMY TEXT DELETE THIS IF MENGGANGGU WKWKWKW",
                'MESSAGE_STATUS' => "23",
                'DESCRIPTION_CODE' => "",
                'RECEIVE_DATETIME' => "2018-01-02 00:00:01",
                'SEND_DATETIME' => "2018-01-02 00:00:01",
                'SENDER' => "Rachmat",
                'USER_ID' => "rachmat",
            ],
            [
                'MESSAGE_ID' => "0GPI2018-01-02 00:00:01.000.uvgjE",
                'DESTINATION' => "628190000001",
                'MESSAGE_CONTENT' => "DUMMY TEXT DELETE THIS IF MENGGANGGU WKWKWKW",
                'MESSAGE_STATUS' => "0+0+0+0",
                'DESCRIPTION_CODE' => "",
                'RECEIVE_DATETIME' => "2018-01-02 00:00:01",
                'SEND_DATETIME' => "2018-01-02 00:00:01",
                'SENDER' => "Rachmat",
                'USER_ID' => "rachmat",
            ]
        ];

        $this->callMethod($report, 'assignMessagePrice', [ApiReport::BILLING_TIERING_BASE, &$message, &$price, &$operators]);
        $this->assertArrayHasKey('OPERATOR', current($message));
        $this->assertArrayHasKey('MESSAGE_COUNT', current($message));
        $this->assertArrayHasKey('PRICE', current($message));
        $this->assertNotEmpty(current($message)['DESCRIPTION_CODE']);
        $this->assertNotEquals("2018-01-02 00:00:01", current($message)['RECEIVE_DATETIME']);
        $this->assertNotEquals("2018-01-02 00:00:01", current($message)['SEND_DATETIME']);
    }

    /**
     * Test formatMessageData method
     *
     * @return void
     */
    public function testFormatMessageDataMethod()
    {
        $report = new ApiReport("2018", "01", true);
        $operators = $this->opertors;

        $message = [
            'MESSAGE_ID' => "0GPI2018-01-02 00:00:01.000.uvgjE",
            'DESTINATION' => "628990000001",
            'MESSAGE_CONTENT' => "DUMMY TEXT DELETE THIS IF MENGGANGGU WKWKWKW",
            'MESSAGE_STATUS' => "23",
            'DESCRIPTION_CODE' => "",
            'RECEIVE_DATETIME' => "2018-01-02 00:00:01",
            'SEND_DATETIME' => "2018-01-02 00:00:01",
            'SENDER' => "Rachmat",
            'USER_ID' => "rachmat",
        ];

        $this->callMethod($report, 'formatMessageData', [&$message, &$operators]);
        $this->assertNotEmpty($message['DESCRIPTION_CODE']);
        $this->assertArrayHasKey('OPERATOR', $message);
        $this->assertArrayHasKey('MESSAGE_COUNT', $message);
        $this->assertNotEquals("2018-01-02 00:00:01", $message['RECEIVE_DATETIME']);
        $this->assertNotEquals("2018-01-02 00:00:01", $message['SEND_DATETIME']);

        /**
         * Test If message content have unicode character
         */
        $message = [
            'MESSAGE_ID' => "0GPI2018-01-02 00:00:01.000.uvgjE",
            'DESTINATION' => "628190000001",
            'MESSAGE_CONTENT' => "DUMMY TEXT DELETE ل THIS IF MENGGANGGU sdadads WKWKWKW",
            'MESSAGE_STATUS' => "23",
            'DESCRIPTION_CODE' => "",
            'RECEIVE_DATETIME' => "2018-01-02 00:00:01",
            'SEND_DATETIME' => "2018-01-02 00:00:01",
            'SENDER' => "Rachmat",
            'USER_ID' => "rachmat",
        ];

        $this->callMethod($report, 'formatMessageData', [&$message, &$operators]);
        $this->assertNotEmpty($message['DESCRIPTION_CODE']);
        $this->assertArrayHasKey('OPERATOR', $message);
        $this->assertArrayHasKey('MESSAGE_COUNT', $message);
        $this->assertEquals($message['MESSAGE_COUNT'], 1);
        $this->assertNotEquals("2018-01-02 00:00:01", $message['RECEIVE_DATETIME']);
        $this->assertNotEquals("2018-01-02 00:00:01", $message['SEND_DATETIME']);
    }

    /**
     * @test
     * Test parseDatetimeInput method
     *
     * @return void
     */
    public function testParseDatetimeInput()
    {
        $report = new ApiReport("2017", "10", true);

        // Test if the value is incorrect and will return current date time string
        $value = "abc";
        $value = $this->callMethod($report, 'parseDatetimeInput', [$value]);
        $this->assertEquals(date('Y-m-d H:i:s'), $value);

        // Test if the value is empty string and will return current date time string
        $value = "";
        $value = $this->callMethod($report, 'parseDatetimeInput', [$value]);
        $this->assertEquals(date('Y-m-d H:i:s'), $value);

        // Test if the value is empty and isServerTimezone is true should return GMT+7 date time string
        $value = "";
        $value = $this->callMethod($report, 'parseDatetimeInput', [$value, true]);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $value);

        // Test the value is timestamp
        $timestamp = "1508976000";
        $value = $this->callMethod($report, 'parseDatetimeInput', [$timestamp]);
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

        // parameter with custom format
        $date = "2017-10-25";
        $customFormat = 'Y-m-d H:i';
        $this->assertEquals("2017-10-25 07:00", $report->clientTimeZone($date, $customFormat));

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
        // parameter with custom format
        $date = "2017-10-25";
        $this->assertEquals("2017-10-24 17:00", $report->serverTimeZone($date, 'Y-m-d H:i'));
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


    /**
     * Test formatPrice method
     *
     * @return  void
     */
    public function testFormatPriceMethod()
    {
        $report = new ApiReport("2018", "10", true);

        // Test if the value is float value and will return 100,000.00
        $value = 100000;
        $value = $this->callMethod($report, 'formatPrice', [$value]);
        $this->assertEquals('100,000.00', $value);

        $value = 100;
        $value = $this->callMethod($report, 'formatPrice', [$value]);
        $this->assertEquals('100.00', $value);

        // Test if the value is not float value
        $value = "abc";
        $value = $this->callMethod($report, 'formatPrice', [$value]);
        $this->assertEquals(0.00, $value);
    }

    /**
     * Test toFloat method
     *
     * @return  void
     */
    public function testToFloatMethod()
    {
        $report = new ApiReport("2018", "10", true);

        // Test if the value is not number value and will return 0
        $value = "abc";
        $value = $this->callMethod($report, 'toFloat', [$value]);
        $this->assertEquals(0, $value);

        // Test if the value is currency string and will return as float value
        $value = "1,400.56";
        $value = $this->callMethod($report, 'toFloat', [$value]);
        $this->assertEquals(1400.56, $value);
    }
}
