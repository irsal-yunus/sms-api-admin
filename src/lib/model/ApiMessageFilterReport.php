<?php

/**
 * ApiMessageFilterReport model to generate message filter report
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

class ApiMessageFilterReport
{

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
    const FINAL_REPORT_FORMAT = ['CATEGORY', 'DELIVERED', 'UNDELIVERED (CHARGED)', 'UNDELIVERED (UNCHARGED)', 'MESSAGES IN TOTAL', 'TOTAL CHARGED (IDR)'];
    const DIR_MESSAGE_CONTENT_REPORT = 'MESSAGE_CONTENT_REPORT';

    /**
     *
     * Public properties
     */
    protected $log,
            $reportDir,
            $reportName,
            $uncategorizedReportName,
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
            $msgFilter,
            $msgFilterReportDir,
            $manifestFile,
            $createdAt,
            $defaultStyle;

    /**
     * server timezone
     *
     * @var type String
     */
    public $timezoneServer = "+0";

    /**
     * client timezone
     *
     * @var type String
     */
    public $timezoneClient = "+7";

    /**
     * ApiMessageContentBasedReport constructor
     * 
     * @param String $month     Report will generate for this month
     * @param String $year      Report will generate for this year
     * @param String $userAPI   User API
     * @param Array $msgFilter  Array that contains Message Content to search and also Department
     */
    public function __construct($month = null, $year = null, $userAPI = null, $msgFilter = [])
    {
        $this->log = Logger::getLogger(get_class($this));
        $this->year = sprintf('%02d', !empty($year) ? $year : date('Y'));
        $this->month = sprintf('%02d', !empty($month) ? $month : date('m'));
        $this->periodSuffix = '_' . date('M_Y', strtotime($this->year . '-' . $this->month));
        $this->userAPI = $userAPI;
        $this->reportDir = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . $this->year . '/' . $this->month . '/';
        $this->createdAt = $this->clientTimeZone(date('Y-m-d'), 'Y-m-d');
        $this->reportName = $this->createdAt . '_' . $this->userAPI . $this->periodSuffix;
        $this->uncategorizedReportName = $this->createdAt . '_' . $this->userAPI . '_Uncategorized' . $this->periodSuffix;
        $this->billingDir = $this->reportDir . 'FINAL_STATUS/';
        $this->billingReport = $this->billingDir . $this->userAPI . $this->periodSuffix . '.xlsx';
        $this->msgFilter = $msgFilter;
        $this->msgFilterReportDir = $this->reportDir . self::DIR_MESSAGE_CONTENT_REPORT . '/';
        $this->manifestFile = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . '.manifest';
    }

    /**
     * Check if specified report is exist
     * 
     * @param String $reportName    full path to specified report
     * @return Boolean
     */
    public function isReportExist($reportName = null)
    {
        return $reportName ? file_exists($reportName) : file_exists($this->billingReport);
    }

    public function getDefaultTraffic($content)
    {
        return [
            'content' => $content,
            'd' => 0, // Delivered
            'udC' => 0, // Undelivered Charged
            'udUc' => 0, // Undelivered Uncharged
            'ts' => 0, // Total SMS
            'cm' => 0, // Total price for charged messages
        ];
    }

    /**
     * Main function to generate message content based Report
     * Will be called from generateMessageContentReport service
     */
    public function generateReport()
    {

        if ($this->isReportExist()) {
            try {
                /**
                 * Billing report reader
                 */
                $reportReader = ReaderFactory::create(Type::XLSX);
                $reportReader->open($this->billingReport);

                $arrResult = [];
                $this->prepareReportData($arrResult);

                $this->createReportFile();

                $this->log->info("Start to generate report " . $this->reportName . " at " . date('Y-m-d H:i:s'));
                /**
                 * Iterate Billing report data from Spreadsheet
                 */
                foreach ($reportReader->getSheetIterator() as $reportIndex => $reportSheet) {
                    foreach ($reportSheet->getRowIterator() as $reportRowIdx => $reportRow) {
                        $isMatch = false;
                        if ($reportRowIdx != 1 && !empty($reportRow)) {
                            //unset($reportRow[""]);
                            $fRow = array_combine(self::DETAILED_MESSAGE_FORMAT, $reportRow) ?: [];

                            /**
                             * One Message from Billing Report will be compared to each message content keyword
                             * Once they match, next message will be compared too
                             * All result will be stored on Array $arrResult
                             * 
                             */
                            foreach ($this->msgFilter as $dept => $value) {
                                foreach ($value as $idx => $content) {
                                    $contentRegex = preg_quote($content);
                                    $keyWord = ['/', '\*'];
                                    $replaceWith = ['\/', '.*'];
                                    $contentRegex = str_replace($keyWord, $replaceWith, $contentRegex);

                                    $contentRegex = '/' . $contentRegex . '/i';

                                    preg_match($contentRegex, $fRow['MESSAGE_CONTENT'], $matches);
                                    if ($matches) {
                                        $this->setTrafficValue($arrResult, $fRow, $dept, $idx);
                                        $this->setTrafficValue($arrResult, $fRow, $dept, 'DEPT_TOTAL');

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
                                $this->setTrafficValue($arrResult, $fRow, 'OTHERS', 0);
                            }
                        }
                    }
                }
                $this->log->info("Finish to loop at " .date('Y-m-d H:i:s'));
                $this->writeReportFile($arrResult);
                $this->log->info("Finish to write at " .date('Y-m-d H:i:s'));
                
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
                $this->log->error('Failed to generate message filter report' . $e);
            }
        } else {
            $this->log->error('File "' . $this->billingReport . '" is not exist!');
        }
    }

    public function prepareReportData(&$arrResult)
    {
        /**
         * Create array key value that the key is array $arrFirstColumn and value are array $traffic
         */
        foreach ($this->msgFilter as $dept => $value) {
            foreach ($value as $idx => $content) {
                $arrResult[$dept][$idx] = $this->getDefaultTraffic($content);
            }
            $arrResult[$dept]['DEPT_TOTAL'] = $this->getDefaultTraffic($dept);
        }

        $arrResult['OTHERS'][0] = $this->getDefaultTraffic('others');
        $arrResult['TOTAL'] = $this->getDefaultTraffic('total');
    }

    public function writeReportFile($arrResult)
    {
        /**
         * Iterate array result then add each value to message content report file
         */
        $this->reportWriter->setActiveSheetIndex(0);
        $startCell = PHPExcel_Cell::stringFromColumnIndex(0);
        $lastCell = PHPExcel_Cell::stringFromColumnIndex(5);
        $row = 3;
        $style = $this->getReportStyle();

        $this->reportWriter
                ->getActiveSheet()
                ->fromArray(self::FINAL_REPORT_FORMAT)
                ->getDefaultStyle()
                ->applyFromArray($style->black);

        foreach ($arrResult as $dept => $val) {
            if ($dept == 'TOTAL' || $dept == 'OTHERS') {
                $rows = $dept == 'TOTAL' ? array_values($val) : array_values($val[0]);
                array_shift($rows);
                array_unshift($rows, $dept);
                $this->reportWriter
                        ->getActiveSheet()
                        ->fromArray($rows, NULL, $startCell . $row, true);
                $row++;
            } else {
                $row_total = array_values($val['DEPT_TOTAL']);
                array_shift($row_total);
                array_unshift($row_total, $dept);
                $this->reportWriter
                        ->getActiveSheet()
                        ->fromArray($row_total, NULL, $startCell . $row, true)
                        ->getStyle($startCell . $row . ':' . $lastCell . $row)
                        ->applyFromArray($style->bold);
                $row++;
                /**
                 * Iterate each message content filter
                 */
                foreach ($val as $idx => $val) {
                    if ($idx !== 'DEPT_TOTAL') {
                        $rows = array_values($val);
                        $content_count = strlen($rows[0]);
                        $rowHeight = $content_count / 50 >= 1 ? ($content_count / 50) * 20 : 20;

                        $this->reportWriter
                                ->getActiveSheet()
                                ->fromArray($rows, NULL, $startCell . $row, true)
                                ->getStyle($startCell . $row)
                                ->applyFromArray($style->right);
                        
                        $this->reportWriter
                                ->getActiveSheet()
                                ->getRowDimension($row)
                                ->setRowHeight($rowHeight);
                        $row++;
                    }
                }
                $row++;
            }
        }

        foreach (range('A', 'F') as $columnID) {
            $width = $columnID == 'A' ? 40 : 25;
            $this->reportWriter->getActiveSheet()->getColumnDimension($columnID)->setWidth($width);
        }

        //  save Final Report
        $writer = PHPExcel_IOFactory::createWriter($this->reportWriter, 'Excel5');
        $writer->save($this->finalReport);

        $this->uncategorizedReportWriter->close();
    }

    private function getReportStyle()
    {
        return (object) [
                    'black' => ['font' => ['name' => 'Arial', 'size' => 8]],
                    'bold' => ['font' => ['bold' => true]],
                    'right' => [
                        'alignment' => ['horizontal' => 'right', 'vertical' => 'center', 'wrap' => true],
                    ],
        ];
    }

    /**
     * Set value for each traffic status that got from message status
     * 
     * @param Array $arrResult
     * @param Array $fRow
     * @param String $rowKey
     */
    public function setTrafficValue(&$arrResult, &$fRow, $dept, $rowKey)
    {
        switch ($fRow['DESCRIPTION_CODE']) {
            case self::SMS_STATUS_DELIVERED:
                $arrResult[$dept][$rowKey]['d'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$dept][$rowKey]['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$dept][$rowKey]['cm'] += $fRow['PRICE'];
                if($rowKey !== 'DEPT_TOTAL'){
                    $arrResult['TOTAL']['d'] += $fRow['MESSAGE_COUNT'];
                    $arrResult['TOTAL']['ts'] += $fRow['MESSAGE_COUNT'];
                    $arrResult['TOTAL']['cm'] += $fRow['PRICE'];
                }
                break;
            case self::SMS_STATUS_UNDELIVERED_CHARGED:
                $arrResult[$dept][$rowKey]['udC'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$dept][$rowKey]['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$dept][$rowKey]['cm'] += $fRow['PRICE'];
                if($rowKey !== 'DEPT_TOTAL'){
                    $arrResult['TOTAL']['udC'] += $fRow['MESSAGE_COUNT'];
                    $arrResult['TOTAL']['ts'] += $fRow['MESSAGE_COUNT'];
                    $arrResult['TOTAL']['cm'] += $fRow['PRICE'];
                }
                break;
            case self::SMS_STATUS_UNDELIVERED:
                $arrResult[$dept][$rowKey]['udUc'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$dept][$rowKey]['ts'] += $fRow['MESSAGE_COUNT'];
                $arrResult[$dept][$rowKey]['cm'] += $fRow['PRICE'];
                if($rowKey !== 'DEPT_TOTAL'){
                    $arrResult['TOTAL']['udUc'] += $fRow['MESSAGE_COUNT'];
                    $arrResult['TOTAL']['ts'] += $fRow['MESSAGE_COUNT'];
                    $arrResult['TOTAL']['cm'] += $fRow['PRICE'];
                }
                break;
        }
    }

    /**
     * Create Message content Report File
     * Create Uncategorized Message Report File
     */
    private function createReportFile()
    {
        try {
            $this->reportWriter = new PHPExcel();
            $this->uncategorizedReportWriter = WriterFactory::create(Type::XLSX);

            $this->finalReport = $this->msgFilterReportDir . $this->reportName . '.xlsx';
            $this->uncategorizedReport = $this->msgFilterReportDir . $this->uncategorizedReportName . '.xlsx';

            is_dir($this->msgFilterReportDir) ?: @mkdir($this->msgFilterReportDir, 0777, true);

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
    private function createReportPackage($finalReport)
    {
        $finalReport = $this->msgFilterReportDir . $this->createdAt . '_' . $this->userAPI . '*.xlsx';

        $this->finalPackage = $this->msgFilterReportDir . $this->reportName . '.zip';

        exec('zip -j ' . $this->finalPackage . ' ' . $finalReport);
    }

    /**
     * Convert DateTime from server timezone to client timezone
     *
     * @param  String $value
     * @return String
     */
    public function clientTimeZone($value, $format = null)
    {
        // If input value is a unix timestamp
        $format = empty($format) ? 'Y-m-d H:i:s' : $format;
        if (is_numeric($value)) {
            $value = date('Y-m-d H:i:s', $value);
        }

        // If input value is not a correct datetime format
        if (!strtotime($value)) {
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
    public function updateManifest($isDone)
    {

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
    public function getManifest()
    {
        return !file_exists($this->manifestFile) ? [] : json_decode(file_get_contents($this->manifestFile));
    }

    /**
     * Download Report
     * 
     * @param String $reportFile    Path to Message Content package report
     * @return Mixed
     */
    public function downloadReport($reportFile)
    {
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
