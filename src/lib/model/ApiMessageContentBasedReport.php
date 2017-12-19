<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiMessageContentBasedReport
 *
 * @author ayu
 */
require_once dirname(dirname(__DIR__)) . '/init.d/init.php';
require_once dirname(dirname(__DIR__)) . '/configs/config.php';
require_once dirname(dirname(__DIR__)) . '/classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once dirname(dirname(__DIR__)) . '/classes/PHPExcel.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class ApiMessageContentBasedReport {

    /**
     * SMS Status which displayed on Billing Report
     */
    const SMS_STATUS_DELIVERED = 'DELIVERED';
    const SMS_STATUS_UNDELIVERED_CHARGED = 'UNDELIVERED (CHARGED)';
    const SMS_STATUS_UNDELIVERED = 'UNDELIVERED (UNCHARGED)';

    /**
     * File report Format
     */
    const DETAILED_MESSAGE_FORMAT = ['MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT', 'ERROR_CODE', 'DESCRIPTION_CODE', 'SEND_DATETIME', 'SENDER', 'USER_ID', 'MESSAGE_COUNT', 'OPERATOR', 'PRICE'];
    const MESSAGE_CONTENT_FORMAT = ['CONTENT', 'DEPARTMENT'];
    const FINAL_REPORT_FORMAT = ['CATEGORY', 'DELIVERED', 'UNDELIVERED (CHARGED)', 'UNDELIVERED (UNCHARGED)', 'MESSAGES IN TOTAL', 'CHARGED MESSAGES'];
    const DIR_MESSAGE_CONTENT_REPORT = 'MESSAGE_CONTENT_REPORT';
    const FINAL_REPORT_NAME = 'Collection_Department';
    const UNCATEGORIZED_REPORT_NAME = 'Uncategorized_Collection_Department';

    /**
     *
     * Public properties
     */
    protected $log,
            $reportDir,
            $reportName,
            $billingDir,
            $billingReport,
            $finalReport,
            $finalPackage,
            $uncategorizedReport,
            $month,
            $year,
            $periodSuffix,
            $userAPI,
            $reportWriter,
            $uncategorizedReportWriter,
            $msgContent,
            $msgContentReportDir,
            $manifestFile;

    /**
     * server timezone
     *
     * @var type String
     */
    public $timezoneServer = "+0";

    /**
     * server timezone
     *
     * @var type String
     */
    public $timezoneClient = "+7";

    /**
     * ApiMessageContentBasedReport constructor
     * 
     * @param String $userAPI   USer API
     * @param Array $msgContent Array that contains Message Content to search and also Department
     */
    public function __construct($userAPI = null, $msgContent = []) {
        $this->log = Logger::getLogger(get_class($this));
        $this->year = sprintf('%02d', date('Y'));
        $this->month = sprintf('%02d', date('m'));
        $this->periodSuffix = '_' . date('M_Y', strtotime($this->year . '-' . $this->month));
        $this->userAPI = $userAPI;
        $this->reportDir = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . $this->year . '/' . $this->month . '/';
        $this->reportName = $this->userAPI . '_' . self::FINAL_REPORT_NAME . $this->periodSuffix . '.xlsx';
        $this->billingDir = $this->reportDir . 'FINAL_STATUS/';
        $this->billingReport = $this->billingDir . $this->userAPI . $this->periodSuffix . '.xlsx';
        $this->msgContent = $msgContent;
        $this->msgContentReportDir = $this->reportDir . self::DIR_MESSAGE_CONTENT_REPORT . '/';
        $this->manifestFile = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . '.manifest';
    }

    /**
     * Check if specified report is exist
     * 
     * @param String $reportName    full path to specified report
     * @return Boolean
     */
    public function isReportExist($reportName = null) {
        return $reportName ? file_exists($reportName) : file_exists($this->billingReport);
    }

    /**
     * Main function to generate message content based Report
     * Will be called from generateMessageContentReport service
     */
    public function generateReport() {
        /**
         * Initialize variable
         */
        $arrFirstColumn = [];
        $i = 0;
        $traffic = [
            'd' => 0, // Delivered
            'udC' => 0, // Undelivered Charged
            'udUc' => 0, // Undelivered Uncharged
            'ts' => 0, // Total SMS
            'cm' => 0, // Total price for charged messages
        ];

        if ($this->isReportExist()) {
            try {
                /**
                 * Billing report reader
                 */
                $reportReader = ReaderFactory::create(Type::XLSX);
                $reportReader->open($this->billingReport);

                /**
                 * Start to create a report File
                 */
                $this->createReportFile();

                /**
                 * Set value as first column on Report
                 */
                foreach ($this->msgContent as $key => $value) {
                    $val = (array) $value;
                    $arrFirstColumn[] = $val['DEPARTMENT'];
                }

                $arrFirstColumn[] = 'OTHERS';
                $arrFirstColumn[] = 'TOTAL';

                /**
                 * Create array key value that the key is array $arrFirstColumn and value are array $traffic
                 */
                $arrResult = array_fill_keys($arrFirstColumn, $traffic);
                $this->log->info("Start to generate report " . $this->reportName . " at " . date('Y-m-d H:i:s'));
                /**
                 * Iterate Billing report data from Spreadsheet
                 */
                foreach ($reportReader->getSheetIterator() as $reportIndex => $reportSheet) {
                    foreach ($reportSheet->getRowIterator() as $reportRowIdx => $reportRow) {
                        $isMatch = false;

                        if ($reportRowIdx != 1 && !empty($reportRow)) {

                            $fRow = array_combine(self::DETAILED_MESSAGE_FORMAT, $reportRow) ?: [];

                            /**
                             * One Message from Billing Report will be compared to each message content keyword
                             * Once they match, next message will be compared too
                             * All result will be stored on Array $arrResult
                             * 
                             */
                            foreach ($this->msgContent as $key => $value) {
                                if (!empty($value)) {
                                    $value = (array) $value;

                                    $contentRegex = preg_quote($value['CONTENT']);
                                    $keyWord = ['/', '\*'];
                                    $replaceWith = ['\/', '.*'];
                                    $contentRegex = str_replace($keyWord, $replaceWith, $contentRegex);

                                    $contentRegex = '/' . $contentRegex . '/i';

                                    preg_match($contentRegex, $fRow['MESSAGE_CONTENT'], $matches);
                                    if ($matches) {
                                        $this->setTrafficValue($arrResult, $fRow, $value['DEPARTMENT']);
                                        $isMatch = true;

                                        break;
                                    }
                                }
                            }

                            /**
                             * Check if message doesn't match with all message content keyword
                             * Add to Uncategorized Message Content Report
                             */
                            if (!$isMatch) {
                                $this->uncategorizedReportWriter->addRow($reportRow);
                                $this->setTrafficValue($arrResult, $fRow, 'OTHERS');
                            }
                        }
                    }
                }

                /**
                 * Iterate array result then add each value to message content report file
                 */
                foreach ($arrResult as $k => $v) {
                    array_unshift($v, $k);
                    $this->reportWriter->addRow(array_values($v));
                }
                $this->reportWriter->close();
                $this->uncategorizedReportWriter->close();

                /**
                 * Create report Package
                 */
                $this->createReportPackage($this->userAPI);

                /**
                 * Update manifest after file is already generated
                 */
                $this->updateManifest(true);

                $this->log->info("Finish to generate report " . $this->reportName . " at " . date('Y-m-d H:i:s'));
            } catch (Exception $e) {
                $this->log->error('Failed to read File');
            }
        } else {
            $this->log->error('File "' . $this->billingReport . '" is not exist!');
        }
    }

    /**
     * Set value for each traffic status that got from message status
     * 
     * @param Array $arrResult
     * @param Array $fRow
     * @param String $rowKey
     */
    public function setTrafficValue(&$arrResult, &$fRow, $rowKey) {

        switch ($fRow['DESCRIPTION_CODE']) {
            case self::SMS_STATUS_DELIVERED:
                $arrResult[$rowKey]['d'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$rowKey]['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$rowKey]['cm'] += $fRow['PRICE'];
                $arrResult['TOTAL']['d'] += $fRow['MESSAGE_COUNT'];
                $arrResult['TOTAL']['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult['TOTAL']['cm'] += $fRow['PRICE'];
                break;
            case self::SMS_STATUS_UNDELIVERED_CHARGED:
                $arrResult[$rowKey]['udC'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$rowKey]['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$rowKey]['cm'] += $fRow['PRICE'];
                $arrResult['TOTAL']['udC'] += $fRow['MESSAGE_COUNT'];
                $arrResult['TOTAL']['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult['TOTAL']['cm'] += $fRow['PRICE'];
                break;
            case self::SMS_STATUS_UNDELIVERED:
                $arrResult[$rowKey]['udUc'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$rowKey]['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$rowKey]['cm'] += $fRow['PRICE'];
                $arrResult['TOTAL']['udUc'] += $fRow['MESSAGE_COUNT'];
                $arrResult['TOTAL']['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult['TOTAL']['cm'] += $fRow['PRICE'];
                break;
        }
    }

    /**
     * Create Message content Report File
     * Create Uncategorized Message Report File
     */
    private function createReportFile() {
        try {
            $this->reportWriter = WriterFactory::create(Type::XLSX);
            $this->uncategorizedReportWriter = WriterFactory::create(Type::XLSX);
            $this->finalReport = $this->msgContentReportDir . $this->reportName;
            $this->uncategorizedReport = $this->msgContentReportDir . $this->userAPI . '_' . self::UNCATEGORIZED_REPORT_NAME . $this->periodSuffix . '.xlsx';

            is_dir($this->msgContentReportDir) ?: @mkdir($this->msgContentReportDir, 0777, true);

            $this->reportWriter->openToFile($this->finalReport);
            $this->reportWriter->addRow(self::FINAL_REPORT_FORMAT);

            $this->uncategorizedReportWriter->openToFile($this->uncategorizedReport);
            $this->uncategorizedReportWriter->addRow(self::DETAILED_MESSAGE_FORMAT);
        } catch (Throwable $e) {
            $this->log->error("Failed to create Message Content Based Report");
        }
    }

    /**
     * Create Zip package
     * 
     * @param String    $userAPI
     */
    private function createReportPackage($finalReport) {
        $finalReport = $this->msgContentReportDir . $this->userAPI . '*.xlsx';

        $this->finalPackage = $this->msgContentReportDir . $this->userAPI . '_' . self::FINAL_REPORT_NAME . $this->periodSuffix . '.zip';

        exec('zip -j ' . $this->finalPackage . ' ' . $finalReport);
    }

    /**
     * Convert DateTime from server timezone to client timezone
     *
     * @param  String $value
     * @return String
     */
    public function clientTimeZone($value, $format = 'Y-m-d H:i:s') {
        // If input value is a unix timestamp
        if (is_numeric($value)) {
            $value = date('Y-m-d H:i:s', $value);
        }

        // If input value is not a correct datetime format
        if(!strtotime($value)){
            $currentTimestamp = strtotime('now');
            $value = date('Y-m-d H:i:s', $currentTimestamp);
        }

        // Create datetime based on input value (GMT)
        $date = new \DateTime($value, new \DateTimeZone($this->timezoneServer));

        // Return datetime corrected for client's timezone (GMT+7)
        return $date->setTimezone(new \DateTimeZone($this->timezoneClient))->format($format);
    }

    /**
     * Function to update manifest file either to add new object or update attribute isDone
     * @param Boolean $isDone       Status of file either done or not
     */
    public function updateManifest($isDone) {

        $manifestContent = [
            'userAPI' => $this->userAPI,
            'reportName' => $this->reportName,
            'reportPackage' => $this->finalPackage,
            'createdAt' => $this->clientTimeZone(date('Y-m-d H:i:s')),
            'isDone' => $isDone
        ];
        $isFound = false;

        if (file_exists($this->manifestFile)) {
            $manifest = $this->getManifest();
            foreach ($manifest as $index => $detail) {
                if ($detail->userAPI == $this->userAPI && $detail->reportName == $this->reportName) {
                    $manifest[$index] = $manifestContent;
                    $isFound = true;
                    break;
                }
            }

            if (!$isFound) {
                $manifest[] = $manifestContent;
            }
        } else {
            $manifest = [$manifestContent];
        }
        try {
            file_put_contents($this->manifestFile, json_encode($manifest));
        } catch (Throwable $e) {
            $this->log->error('Failed to append to .manifest file');
        }
    }

    /**
     * Get list of report from Manifest File
     * @return Array
     */
    public function getManifest() {
        return !file_exists($this->manifestFile) ? [] : json_decode(file_get_contents($this->manifestFile));
    }

    /**
     * Download Report
     * 
     * @param String $reportFile    Path to Message Content package report
     * @return Mixed
     */
    public function downloadReport($reportFile) {
        if ($this->isReportExist($reportFile)) {

            // Zip transfer 
            ob_start();
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: public');
            header('Content-Description: File Transfer');
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($reportFile) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($reportFile));
            ob_end_flush();
            @readfile($reportFile);
        } else {
            $this->log->warn('Could not download report, file not found: ' . $reportFile);
            return false;
        }
    }

}
