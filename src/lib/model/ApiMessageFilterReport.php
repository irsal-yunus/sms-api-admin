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
    const DETAILED_MESSAGE_FORMAT = ['MESSAGE_ID', 'DESTINATION', 'MESSAGE_CONTENT', 'ERROR_CODE', 'DESCRIPTION_CODE', 'RECEIVE_DATETIME', 'SEND_DATETIME', 'SENDER', 'USER_ID', 'MESSAGE_COUNT', 'OPERATOR', 'PRICE'];
    const MESSAGE_CONTENT_FORMAT = ['CONTENT', 'DEPARTMENT'];
    const FINAL_REPORT_FORMAT = ['CATEGORY', 'DELIVERED', 'UNDELIVERED (CHARGED)', 'UNDELIVERED (UNCHARGED)', 'MESSAGES IN TOTAL', 'TOTAL CHARGED (IDR)'];
    const DIR_MESSAGE_CONTENT_REPORT = 'MESSAGE_CONTENT_REPORT';
    const DEPT_TOTAL = 'DEPT_TOTAL';
    const OTHERS = 'OTHERS';
    const TOTAL = 'TOTAL';
    
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
            $billingReportCSV,
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
        $this->billingReportCSV = $this->billingDir . $this->userAPI . $this->periodSuffix . '.csv';
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

    /**
     * Function that return traffic value on Report
     * 
     * @param String $content
     * @return Array
     */
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
     * 
     * @return void
     */
    public function generateReport()
    {

        if ($this->isReportExist()) {
            $this->log->info("Start to generate report " . $this->reportName . " at " . date('Y-m-d H:i:s'));

            /**
             * Convert XLSX File to CSV
             * CSV file is faster to load and read, but Billing report is XLSX file so need to convert
             * Each sheet on Excel file will be convert into separate CSV file
             */
            $this->convertXLStoCSV();

            $arrResult = [];

            /**
             * Prepare Message Filter Data
             * Mapping to array to make it easier to iterate
             */
            $this->prepareReportData($arrResult);

            /**
             * Create Message Filter Report File
             * and Uncategorized Report File
             */
            $this->createReportFile();

            /**
             * Get all billing report that already convert into CSV file
             */
            $csvFiles = $this->getCSVFiles();
            /**
             * Iterate Billing report data from Spreadsheet
             * Save the data on Array result $arrResult
             */
            foreach ($csvFiles as $file) {
                $reportReader = ReaderFactory::create(Type::CSV);
                $reportReader->open($file);

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
                                        $this->setTrafficValue($arrResult, $fRow, $dept, self::DEPT_TOTAL);

                                        $isMatch = true;

                                        break;
                                    }
                                }

                                if ($isMatch) {
                                    break;
                                }
                            }

                            /**
                             * Check if message doesn't match with all message content keyword
                             * Add to Uncategorized Message Content Report
                             */
                            if (!$isMatch) {
                                $this->uncategorizedReportWriter->addRow($reportRow);
                                $this->setTrafficValue($arrResult, $fRow, self::OTHERS, 0);
                            }
                        }
                    }
                }
            }

            /**
             * Delete Billing Report on CSV
             */
            foreach ($csvFiles as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            
            if(is_dir($this->billingReportCSV)){
                rmdir($this->billingReportCSV);
            }
            /**
             * Write Summarize result to Message Filter Report
             */
            $this->writeReportFile($arrResult);

            /**
             * Create report Package includes Message Filter Report and Uncategorized Message Report
             */
            $this->createReportPackage($this->userAPI);

            /**
             * Update manifest after file has generated successfully
             */
            $this->updateManifest(true);

            $this->log->info("Finish to generate report " . $this->reportName . " at " . date('Y-m-d H:i:s'));
        } else {
            $this->log->error('File "' . $this->billingReport . '" is not exist!');
        }
    }

    /**
     * Function to convert XLSX file to CSV file
     * Execute Shell command to convert (required Gnumeric)
     * 
     * @return Mixed
     */
    protected function convertXLStoCSV()
    {
        if (file_exists($this->billingReport) && filesize($this->billingReport) > 0) {
            return exec("xlsx2csv -a " . $this->billingReport . " " . $this->billingReportCSV);
        }

        return null;
    }
    
    /**
     * Get billing report that already convert to CSV file
     * 
     * @return Array CSV files
     */

    protected function getCSVFiles()
    {
        $billingCSVFiles = [];
        foreach (glob($this->billingReportCSV . '/'.'*.csv') as $filename) {
            $billingCSVFiles[] = $filename;
        }

        return $billingCSVFiles;
    }

    /**
     * Function to mapping message filter into Array Key Value $arrResult
     * 
     * @param Array $arrResult
     * @return void
     */
    protected function prepareReportData(&$arrResult)
    {
        /**
         * Create array key value that the key is array $arrFirstColumn and value are array $traffic
         */
        foreach ($this->msgFilter as $dept => $value) {
            foreach ($value as $idx => $content) {
                $arrResult[$dept][$idx] = $this->getDefaultTraffic($content);
            }
            $arrResult[$dept][self::DEPT_TOTAL] = $this->getDefaultTraffic($dept);
        }

        $arrResult[self::OTHERS][0] = $this->getDefaultTraffic('others');
        $arrResult[self::TOTAL] = $this->getDefaultTraffic('total');
    }

    /**
     * Function to write summarize result to Message Filter Report
     * Apply Cell Styling
     * 
     * @param Array $arrResult
     * @return void
     */
    protected function writeReportFile($arrResult)
    {
        /**
         * Iterate array result then add each value to message content report file
         */
        $sheet = $this->reportWriter->setActiveSheetIndex(0);
        $startCell = PHPExcel_Cell::stringFromColumnIndex(0);
        $lastCell = PHPExcel_Cell::stringFromColumnIndex(5);
        $row = 3;
        $style = $this->getReportStyle();

        $sheet->fromArray(self::FINAL_REPORT_FORMAT)
                ->getDefaultStyle()
                ->applyFromArray($style->black);

        foreach ($arrResult as $dept => $val) {
            if ($dept == self::TOTAL || $dept == self::OTHERS) {
                $rows = $dept == self::TOTAL ? array_values($val) : array_values($val[0]);
                array_shift($rows);
                array_unshift($rows, $dept);
                $sheet->fromArray($rows, NULL, $startCell . $row, true);

                $row++;
            } else {
                $row_total = array_values($val[self::DEPT_TOTAL]);
                array_shift($row_total);
                array_unshift($row_total, $dept);
                $sheet->fromArray($row_total, NULL, $startCell . $row, true)
                        ->getStyle($startCell . $row . ':' . $lastCell . $row)
                        ->applyFromArray($style->bold);
                $row++;
                /**
                 * Iterate each message content filter
                 */
                foreach ($val as $idx => $val) {
                    if ($idx !== self::DEPT_TOTAL) {
                        $rows = array_values($val);
                        $content_count = strlen($rows[0]);
                        $rowHeight = $content_count / 50 >= 1 ? ($content_count / 50) * 20 : 20;

                        $sheet->fromArray($rows, NULL, $startCell . $row, true)
                                ->getStyle($startCell . $row)
                                ->applyFromArray($style->right);

                        $sheet->getRowDimension($row)
                                ->setRowHeight($rowHeight);
                        $row++;
                    }
                }
                $row++;
            }
        }

        foreach (range('A', 'F') as $columnID) {
            $width = $columnID == 'A' ? 40 : 25;
            $sheet->getColumnDimension($columnID)->setWidth($width);
        }

        //  save Final Report
        $writer = PHPExcel_IOFactory::createWriter($this->reportWriter, 'Excel2007');
        $writer->save($this->finalReport);

        $this->uncategorizedReportWriter->close();
    }

    /**
     * Function that return default cell style
     * @return stdClass
     */
    protected function getReportStyle()
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
                if($rowKey !== self::DEPT_TOTAL){
                    $arrResult['TOTAL']['d'] += $fRow['MESSAGE_COUNT'];
                }
                break;
            case self::SMS_STATUS_UNDELIVERED_CHARGED:
                $arrResult[$dept][$rowKey]['udC'] += $fRow['MESSAGE_COUNT'];
                if($rowKey !== self::DEPT_TOTAL){
                    $arrResult['TOTAL']['udC'] += $fRow['MESSAGE_COUNT'];
                }
                break;
            case self::SMS_STATUS_UNDELIVERED:
                $arrResult[$dept][$rowKey]['udUc'] += $fRow['MESSAGE_COUNT'];
                if($rowKey !== self::DEPT_TOTAL){
                    $arrResult['TOTAL']['udUc'] += $fRow['MESSAGE_COUNT'];
                }
                break;
        }

        $arrResult[$dept][$rowKey]['ts'] += $fRow['MESSAGE_COUNT'];
        $arrResult[$dept][$rowKey]['cm'] += $fRow['PRICE'];
        if ($rowKey !== self::DEPT_TOTAL) {
            $arrResult['TOTAL']['ts'] += $fRow['MESSAGE_COUNT'];
            $arrResult['TOTAL']['cm'] += $fRow['PRICE'];
        }
    }

    /**
     * Create Message content Report File
     * Create Uncategorized Message Report File
     * 
     * @return void
     */
    protected function createReportFile()
    {
        $this->reportWriter = new PHPExcel();
        $this->reportWriter
                ->getProperties()
                ->setCreator("Firstwap")
                ->setTitle($this->reportName);

        $this->uncategorizedReportWriter = WriterFactory::create(Type::XLSX);

        $this->finalReport = $this->msgFilterReportDir . $this->reportName . '.xlsx';
        $this->uncategorizedReport = $this->msgFilterReportDir . $this->uncategorizedReportName . '.xlsx';

        is_dir($this->msgFilterReportDir) ?: @mkdir($this->msgFilterReportDir, 0777, true);

        $this->uncategorizedReportWriter->openToFile($this->uncategorizedReport);
        $this->uncategorizedReportWriter->addRow(self::DETAILED_MESSAGE_FORMAT);
    }

    /**
     * Create Zip package
     * 
     * @param String    $userAPI
     * @return void
     */
    private function createReportPackage()
    {
        $finalReport = $this->msgFilterReportDir . $this->createdAt . '_' . $this->userAPI . '*.xlsx';

        $this->finalPackage = $this->msgFilterReportDir . $this->reportName . '.zip';

        exec('zip -j -m ' . $this->finalPackage . ' ' . $finalReport);
    }

    /**
     * Convert DateTime from server timezone to client timezone
     *
     * @param String $value
     * @param String $format
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
     * @return Mixed return the number of bytes that were written to the file, or FALSE on failure
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

        return file_put_contents($this->manifestFile, json_encode($manifest));
    }

    /**
     * Get list of report from Manifest File
     * @return Array
     */
    public function getManifest()
    {
        $manifest = !file_exists($this->manifestFile) ? [] : json_decode(file_get_contents($this->manifestFile));
        usort($manifest, function($a, $b) {
            $ad = new DateTime($a->createdAt);
            $bd = new DateTime($b->createdAt);

            if ($ad == $bd) {
                return 0;
            }

            return $ad > $bd ? -1 : 1;
        });

        return $manifest;
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
