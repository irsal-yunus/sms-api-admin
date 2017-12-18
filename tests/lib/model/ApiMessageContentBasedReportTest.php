<?php

use Firstwap\SmsApiAdmin\Test\TestCase;

require_once dirname(dirname(dirname(__DIR__))) . '/src/init.d/init.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/lib/model/ApiMessageContentBasedReport.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once dirname(dirname(dirname(__DIR__))) . '/src/classes/PHPExcel.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

/**
 * Description of ApiMessageContentBasedReportTest
 *
 * @author ayu
 */
class ApiMessageContentBasedReportTest extends TestCase {

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
    public function setUp() {
        parent::setUp();

        $this->userAPI = 'test_api';
        $this->messageContent = '[{"CONTENT":"Dear Adira, kode reset Aplikasi Anda Adalah *","DEPARTMENT":"Dept Collection"},{"CONTENT":"Selamat! Anda terpilih mendapatkan pembiayaan Gadget di toko *","DEPARTMENT":"Strategic Marketing Department"}]';
        $this->apiModel = new ApiMessageContentBasedReport($this->userAPI, json_decode($this->messageContent));
        $this->periodSuffix = '_' . date('M_Y', strtotime(date('Y') . '-' . date('m')));

        /**
         * Information about report path and report name
         */
        $this->reportDir = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . date('Y') . '/' . date('m') . '/';
        $this->billingReport = $this->reportDir . 'FINAL_STATUS/' . $this->userAPI . $this->periodSuffix . '.xlsx';
        $this->msgContentReportDir = $this->reportDir . 'MESSAGE_CONTENT_REPORT/';
        $this->finalReport = $this->msgContentReportDir . $this->userAPI . '_Collection_Department' . $this->periodSuffix . '.xlsx';
        $this->uncategorizedReport = $this->msgContentReportDir . $this->userAPI . '_Uncategorized_Collection_Department' . $this->periodSuffix . '.xlsx';
        $this->finalPackage = $this->msgContentReportDir . $this->userAPI . '_Collection_Department' . $this->periodSuffix . '.zip';
        $this->manifestFile = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . '.manifest';
    }

    /**
     * Rollback operation on setUp
     * Delete all file report that generated during test
     */
    public function tearDown() {

        parent::tearDown();
        try {
            unlink($this->finalPackage);
            unlink($this->billingReport);
            unlink($this->finalReport);
            unlink($this->uncategorizedReport);
            
            $this->deleteManifest();
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Function to copy temporary billing report file
     * This file will be used on another test cases
     */
    public function copyTempReportFile() {
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
    public function deleteManifest() {
        $manifestContent = json_decode(file_get_contents($this->manifestFile));
        foreach($manifestContent as $key => $value){
            if($value->userAPI == $this->userAPI){
                unset($manifestContent[$key]);
            }
        }
        file_put_contents($this->manifestFile, json_encode($manifestContent));
    }

    /**
     * Test case to check that specified report is exist
     * Assert true if file is exist
     */
    public function testIsReportExist() {
        $this->copyTempReportFile();

        $result = $this->callMethod($this->apiModel, 'isReportExist', [$this->billingReport]);
        $this->assertTrue($result);
    }

    /**
     * Negative test case to check if specified file is exist
     * Assert false if file is not exist
     */
    public function testIsReportExistWithUnknownFile() {
        $billingReportFile = $this->billingReportDir . 'test.xlsx';

        $result = $this->callMethod($this->apiModel, 'isReportExist', [$billingReportFile]);
        $this->assertFalse($result);
    }

    /**
     * Test case for createReportFile function
     * Function will create Message Content Report directory is it's not exist
     * Function will create Message Content Report File and Uncategorized Report
     * Assert True if those file and folder is exist
     */
    public function testCreateReportFile() {
        $this->callMethod($this->apiModel, 'createReportFile', []);

        $this->assertTrue(is_dir($this->msgContentReportDir));
        $this->assertFileExists($this->finalReport);
        $this->assertFileExists($this->uncategorizedReport);
    }

    /**
     * Test case for function createReportPackage
     * Function will create package report
     * Assert true if package report is exist
     */
    public function testCreateReportPackage() {
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
    public function testUpdateManifest() {
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
    public function testGetManifest() {
        $params = [$this->userAPI, $this->finalReport, false];
        $this->callMethod($this->apiModel, 'updateManifest', $params);
        
        $result = $this->callMethod($this->apiModel, 'getManifest', []);
        $this->assertNotEmpty($result);
    }

    /**
     * Test case for function setTrafficValue
     * Set dummy data, then check if the number of delivered, undelivered(charged) and undelivered(uncharged)
     * message that set on array result is equals with params
     */
    public function testSetTrafficValue() {
        $traffic = [
            'd' => 0, // Delivered
            'udC' => 0, // Undelivered Charged
            'udUc' => 0, // Undelivered Uncharged
            'ts' => 0, // Total SMS
            'cm' => 0, // Total price for charged messages
        ];
        
        $listDepartment = [
            'DepartmentA' => (object) ['status'=>'DELIVERED', 'traffic'=>'d'],
            'DepartmentB' => (object) ['status'=>'UNDELIVERED (CHARGED)', 'traffic'=>'udC'],
            'DepartmentC' => (object) ['status'=>'UNDELIVERED (UNCHARGED)', 'traffic'=>'udUc'],
        ];
        
        $row = [
            'DESCRIPTION_CODE' => '',
            'MESSAGE_COUNT' => 1,
            'PRICE' => 200
        ];
       
        foreach($listDepartment as $key => $val){
            $keys = [$key, 'OTHERS', 'TOTAL'];
            $column = array_fill_keys($keys, $traffic);
            $row['DESCRIPTION_CODE'] = $val->status;
            $params = [&$column, &$row, $key];
            
            $this->callMethod($this->apiModel, 'setTrafficValue', $params);
            $this->assertEquals($row['MESSAGE_COUNT'], $column[$key][$val->traffic]);
        }

    }

    /**
     * Test case for function generateReport
     * This function should generate report
     */
    public function testGenerateReport() {
        $this->copyTempReportFile();

        $result = $this->callMethod($this->apiModel, 'generateReport', []);
        $this->assertFileExists($this->finalReport);
        $this->assertFileExists($this->uncategorizedReport);
        $this->assertFileExists($this->finalPackage);
        
    }

    /**
     * @runInSeparateProcess
     * Test case for function downloadReport
     * First create the report package and reportFile by calling createReportPackage and createReportFile method
     * Then call downloadReport method and assert that header contains attachment of report package
     */
    public function testDownloadReport() {
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
    public function testDownloadReportWithoutReportParams(){
        $this->callMethod($this->apiModel, 'createReportFile', []);
        $this->callMethod($this->apiModel, 'createReportPackage', []);

        $response = $this->callMethod($this->apiModel, 'downloadReport', []);
        $this->assertFalse($response);
    }

}
