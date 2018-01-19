<?php

use Firstwap\SmsApiAdmin\Test\TestCase;

require_once dirname(dirname(dirname(__DIR__))) . '/src/init.d/init.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/lib/model/ApiMessageFilterReport.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/classes/PHPExcel.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

/**
 * Description of ApiMessageContentBasedReportTest
 *
 * @author ayu
 */
class ApiMessageFilterReportTest extends TestCase
{

    public $userAPI,
            $messageContent,
            $reportWriter,
            $billingReportDir,
            $billingReport,
            $msgContentReportDir,
            $uncategorizedReport,
            $finalReport,
            $finalPackage,
            $manifestFile,
            $periodSuffix,
            $apiModel;

    /**
     * Set up the test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->userAPI = 'test_api';
        $this->messageContent = '{"Gadget Department":["Selamat! Anda terpilih mendapatkan pembiayaan Gadget*"]}';
        $this->apiModel = new ApiMessageFilterReport('', '', $this->userAPI, json_decode($this->messageContent));
        $this->periodSuffix = '_' . date('M_Y', strtotime(date('Y') . '-' . date('m')));

        /**
         * Information about report path and report name
         */
        $this->reportDir = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . date('Y') . '/' . date('m') . '/';
        $this->billingReport = $this->reportDir . 'FINAL_STATUS/' . $this->userAPI . $this->periodSuffix . '.xlsx';
        $this->billingReportCSV = $this->reportDir . 'FINAL_STATUS/' . $this->userAPI . $this->periodSuffix . '.csv';

        $this->msgContentReportDir = $this->reportDir . 'MESSAGE_CONTENT_REPORT/';
        $this->createdAt = date('Y-m-d');
        $this->finalReport = $this->msgContentReportDir . $this->createdAt . '_' . $this->userAPI . $this->periodSuffix . '.xlsx';
        $this->uncategorizedReport = $this->msgContentReportDir . $this->createdAt . '_' . $this->userAPI . '_Uncategorized' . $this->periodSuffix . '.xlsx';
        $this->finalPackage = $this->msgContentReportDir . $this->createdAt . '_' . $this->userAPI . $this->periodSuffix . '.zip';
        $this->manifestFile = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . '.manifest';
    }

    /**
     * Rollback operation on setUp
     * Delete all file report that generated during test
     */
    public function tearDown()
    {

        parent::tearDown();
        try {
            !file_exists($this->finalPackage) ? : unlink($this->finalPackage);
            !file_exists($this->finalPackage) ? : unlink($this->billingReport);
            !file_exists($this->finalPackage) ? : unlink($this->finalReport);
            !file_exists($this->finalPackage) ? : unlink($this->uncategorizedReport);
            
            $this->deleteManifest();
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Function to copy temporary billing report file
     * This file will be used on another test cases
     */
    public function copyTempReportFile()
    {
        try {
            $file = dirname(dirname(__DIR__)) . '/resources/test_api.xlsx';
            $newfile = $this->billingReport;
            if (!file_exists($newfile)) {
                if (!copy($file, $newfile)) {
                    echo "failed to copy $file...\n";
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Helper function to delete specific user api from manifest file
     * This function is called on tearDown
     */
    public function deleteManifest()
    {
        $manifestContent = json_decode(file_get_contents($this->manifestFile));
        foreach ($manifestContent as $key => $value) {
            if ($value->userAPI == $this->userAPI) {
                unset($manifestContent[$key]);
            }
        }
        file_put_contents($this->manifestFile, json_encode($manifestContent));
    }

    /**
     * Test case to check that specified report is exist
     * Assert true if file is exist
     */
    public function testIsReportExist()
    {
        $this->copyTempReportFile();

        $result = $this->callMethod($this->apiModel, 'isReportExist', [$this->billingReport]);
        $this->assertTrue($result);
    }

    /**
     * Negative test case to check if specified file is exist
     * Assert false if file is not exist
     */
    public function testIsReportExistWithUnknownFile()
    {
        $billingReportFile = $this->billingReportDir . 'test.xlsx';

        $result = $this->callMethod($this->apiModel, 'isReportExist', [$billingReportFile]);
        $this->assertFalse($result);
    }
    
    /**
     * Test case to get default traffic value for Billing Report
     * Assert true if return value has key content
     */
    public function testGetDefaultTraffic()
    {
        $params = ['Kode reset aplikasi anda adalah *'];
        $result = $this->callMethod($this->apiModel, 'getDefaultTraffic', $params);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertEquals($params[0], $result['content']);
    }
    
    /**
     * Test case for function convertXLStoCSV
     * Function will convert Excel billing report to CSV file
     * Assert true if CSV files is exist
     */
    public function testConvertXLStoCSV()
    {
        $this->copyTempReportFile();

        $result = $this->callMethod($this->apiModel, 'convertXLStoCSV', []);
        foreach (glob($this->billingReportCSV . '.*') as $filename) {
            $this->assertFileExists($filename);
        }
    }
    
    /**
     * Test case for function convertXLStoCSV 
     * But with empty billing report
     * Assert if CSV file is not exist
     */
    public function testConvertXLStoCSVwithEmptyFile()
    {
        if(file_exists($this->billingReport)){
            unlink($this->billingReport);
        }
        
        $result = $this->callMethod($this->apiModel, 'convertXLStoCSV', []);
        $this->assertFileNotExists($this->billingReportCSV);
        $this->assertNull($result);
        
    }
    
    /**
     * Test case for function getCSVFiles
     * Function will return array of file name
     * Assert if result if not empty
     */
    public function testGetCSVFiles()
    {
        $this->copyTempReportFile();

        $result = $this->callMethod($this->apiModel, 'getCSVFiles', []);
        $this->assertNotEmpty($result);
    }

    /**
     * Test case for createReportFile function
     * Function will create Message Content Report directory is it's not exist
     * Function will create Message Content Report File and Uncategorized Report
     * Assert True if those file and folder is exist
     */
    public function testCreateReportFile()
    {
        $this->callMethod($this->apiModel, 'createReportFile', []);

        $this->assertTrue(is_dir($this->msgContentReportDir));
        $this->assertFileExists($this->uncategorizedReport);
    }

    /**
     * Test case for function createReportPackage
     * Function will create package report
     * Assert true if package report is exist
     */
    public function testCreateReportPackage()
    {
        $this->callMethod($this->apiModel, 'createReportFile', []);

        $this->callMethod($this->apiModel, 'createReportPackage', []);
        $this->assertFileExists($this->finalPackage);
    }

    /**
     * Test case for function updateManifest
     * Function will create manifest file if not exist
     * Function will append string JSON
     * Assert true if manifest file is exist and manifest content is not Empty
     */
    public function testUpdateManifest()
    {
        $params = [$this->userAPI, $this->finalReport, false];
        $this->callMethod($this->apiModel, 'updateManifest', $params);
        $this->assertFileExists($this->manifestFile);

        $manifestContent = json_decode(file_get_contents($this->manifestFile));
        $this->assertNotEmpty($manifestContent);
    }
    
    /**
     * Test case for function update manifest with existing key (api user)
     * Assert if return value is not empty
     */
    public function testUpdateManifestWithExistingKeyArray(){
        $params = [$this->userAPI, $this->finalReport, true];
        $this->callMethod($this->apiModel, 'updateManifest', $params);
        $this->assertFileExists($this->manifestFile);

        $manifestContent = json_decode(file_get_contents($this->manifestFile));
        $this->assertNotEmpty($manifestContent);
    }
    
    /**
     * Test case for function update manifest file without manifest file
     * Assert if manifest file is exist
     */
    public function testUpdateManifestWithoutExistingFile(){
        
        if(file_exists($this->manifestFile)){
            unlink($this->manifestFile);
        }
        
        $params = [$this->userAPI, $this->finalReport, false];
        $this->callMethod($this->apiModel, 'updateManifest', $params);
        $this->assertFileExists($this->manifestFile);

        $manifestContent = json_decode(file_get_contents($this->manifestFile));
        $this->assertNotEmpty($manifestContent);
    }
    
    /**
     * Test case for function getManifest
     * Function will return content of manifest file 
     * and will return empty array if manifest file is empty
     */
    public function testGetManifest()
    {
        $params = [$this->userAPI, $this->finalReport, false];
        $this->callMethod($this->apiModel, 'updateManifest', $params);

        $result = $this->callMethod($this->apiModel, 'getManifest', []);
        $this->assertNotEmpty($result);
    }

    /**
     * Test case for function prepareReportData
     * with parameter array result
     * Function will modify array result
     * Assert if array result is not empty
     */
    public function testPrepareReportData()
    {
        $arrResult = [];
        $this->callMethod($this->apiModel, 'prepareReportData', [&$arrResult]);
        $this->assertNotEmpty($arrResult);
        $this->assertArrayHasKey('TOTAL', $arrResult);
    }
    
    /**
     * Test case for function getReportStyle
     * Function will return object that contains Cell styling
     * Assert if result object has attribute bold, black and right
     */
    public function testGetReportStyle()
    {
        $result = $this->callMethod($this->apiModel, 'getReportStyle', []);
        $this->assertObjectHasAttribute('bold', $result);
        $this->assertObjectHasAttribute('black', $result);
        $this->assertObjectHasAttribute('right', $result);
    }

    /**
     * Test case for function setTrafficValue
     * Set dummy data, then check if the number of delivered, undelivered(charged) and undelivered(uncharged)
     * message that set on array result is equals with params
     */
    public function testSetTrafficValue()
    {
        $rows = [
            [
                'DESCRIPTION_CODE' => 'DELIVERED',
                'MESSAGE_COUNT' => 2,
                'PRICE' => 400
            ],
            [
                'DESCRIPTION_CODE' => 'UNDELIVERED (CHARGED)',
                'MESSAGE_COUNT' => 3,
                'PRICE' => 600
            ],
            [
                'DESCRIPTION_CODE' => 'UNDELIVERED (UNCHARGED)',
                'MESSAGE_COUNT' => 1,
                'PRICE' => 200
            ]
        ];

        $column = [
            'Gadget Department' => [
                [
                    'content' => 'Selamat! Anda terpilih mendapatkan pembiayaan Gadget*',
                    'd' => 0,
                    'udC' => 0,
                    'udUc' => 0,
                    'ts' => 0,
                    'cm' => 0
                ]
            ],
            'TOTAL' => [
                'content' => 'Total',
                'd' => 0,
                'udC' => 0,
                'udUc' => 0,
                'ts' => 0,
                'cm' => 0
            ]
        ];
        
        $status = [
            'DELIVERED' => 'd',
            'UNDELIVERED (CHARGED)' => 'udC',
            'UNDELIVERED (UNCHARGED)' => 'udUc'
        ];
        
        $msgContent = json_decode($this->messageContent);
        $i = 0;
        
        foreach($rows as $row){
            foreach ($msgContent as $dept => $value) {
                $msgCount  = 0;
                foreach ($value as $idx => $content) {
                    $params = [&$column, &$row, $dept, $idx];
                    $msgCount += $row['MESSAGE_COUNT'];
                    $this->callMethod($this->apiModel, 'setTrafficValue', $params);
                    $this->assertEquals($row['MESSAGE_COUNT'], $column[$dept][$idx][$status[$row['DESCRIPTION_CODE']]]);
                }
            }
        }
    }
    
    /**
     * Test case for function clientTimeZone
     * Test case is designed for several format of date
     */
    public function testClientTimeZone()
    {
        // parameter is correct format date
        $date = "2017-10-25";
        $this->assertEquals("2017-10-25 07:00:00", $this->callMethod($this->apiModel, 'clientTimeZone', [$date]));
        
        //with custom format
        $date = "2017-10-25";
        $customFormat = 'Y-m-d H:i';
        $this->assertEquals("2017-10-25 07:00", $this->callMethod($this->apiModel, 'clientTimeZone', [$date, $customFormat]));

        // Test timezone correctoin of 7 hours
        $date = "2017-10-26 08:00:00";
        $this->assertEquals("2017-10-26 15:00:00", $this->callMethod($this->apiModel, 'clientTimeZone', [$date]));
        
        // Test if the timezone correction works when GMT is a day behind GMT+7
        $date = "2017-10-26 23:00:00";
        $this->assertEquals("2017-10-27 06:00:00", $this->callMethod($this->apiModel, 'clientTimeZone', [$date]));
        
        // parameter is timestamp
        $date = "1508976000";
        $this->assertEquals("2017-10-26 07:00:00", $this->callMethod($this->apiModel, 'clientTimeZone', [$date]));
        
        // Test if the current time is returned if the input parameter is an empty string
        $date = "";
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $this->callMethod($this->apiModel, 'clientTimeZone', [$date]));
        
        // Test if the current time is returned if the input parameter is null
        $date = null;
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $this->callMethod($this->apiModel, 'clientTimeZone', [$date]));
        
        // Test if the current time is returned if the input parameter is incorrect
        $date = "abc";
        $this->assertEquals(date('Y-m-d H:i:s', strtotime("+7 hours")), $this->callMethod($this->apiModel, 'clientTimeZone', [$date]));
    }


    /**
     * Test case for function generateReport
     * This function should generate report
     * Assert if Message Filter Report is exist
     */
    public function testGenerateReport()
    {
        $this->copyTempReportFile();

        $result = $this->callMethod($this->apiModel, 'generateReport', []);
        $this->assertFileExists($this->finalPackage);
    }
    
    /**
     * Test case for function generateReport
     * but with unexisting billing report file
     * Assert if Message Content Report is not exist
     */
    public function testGenerateReportWithUnexistingBillingReport(){
        if(file_exists($this->billingReport)){
            unlink($this->billingReport);
        }
        $result = $this->callMethod($this->apiModel, 'generateReport', []);
        $this->assertFileNotExists($this->finalPackage);
    }

    /**
     * @runInSeparateProcess
     * Test case for function downloadReport
     * First create the report package and reportFile by calling createReportPackage and createReportFile method
     * Then call downloadReport method and assert that header contains attachment of report package
     */
    public function testDownloadReport()
    {
        $this->callMethod($this->apiModel, 'createReportFile', []);
        $this->callMethod($this->apiModel, 'createReportPackage', []);

        $this->callMethod($this->apiModel, 'downloadReport', [$this->finalPackage]);

        $this->assertContains(
                'Content-Disposition: attachment; filename="' . basename($this->finalPackage) . '"', xdebug_get_headers()
        );
    }

    /**
     * Negative test case for function downloadReport without report file as params
     * Function will return false
     */
    public function testDownloadReportWithoutReportParams()
    {
        $this->callMethod($this->apiModel, 'createReportFile', []);
        $this->callMethod($this->apiModel, 'createReportPackage', []);

        $response = $this->callMethod($this->apiModel, 'downloadReport', []);
        $this->assertFalse($response);
    }
}
