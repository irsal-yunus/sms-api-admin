<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * @author Fathir Wafda
 * 
 */

// Exception Handler
//class CustomExceptionHandler{
//    public $logger;
//    public $info;
//
//    public function __construct() {
//        $this->logger = Logger::getRootLogger();
//    }
//
//    public function handler($errno, $errstr, $errfile, $errline, $a){
//        if (!(error_reporting() & $errno)) {
//            return;
//        }
//
//        switch ($errno) {
//            case E_USER_ERROR:
//                $this->logger->error("[$errno] $errstr ".$this->info);
//                exit(1);
//                break;
//
//            case E_USER_WARNING:
//                $this->logger->warn("[$errno] $errstr ".$this->info);
//                break;
//
//            case E_USER_NOTICE:
//                $this->logger->info("[$errno] $errstr ".$this->info);
//                break;
//
//            case E_WARNING:
//                $this->logger->error("[$errno] $errstr ".$this->info);
//            break;
//
//            default:
//                $this->logger->info("[$errno] $errstr ".$this->info);
//            break;
//        }
//        return true;
//    }        
//}


require_once '../classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once '../configs/config.php';
require_once '../init.d/init.php';

//use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;

class ExcelSpout extends ApiBaseModel {
    
    public  $logger;
    
    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getRootLogger();
    }

     /**
     * 
     * @param type $userName
     * @param type $lsReport
     */
    public function getDataScheduled($userName, $lsReport) {
        //$exception = new CustomExceptionHandler();
        //set_error_handler(array($exception,'handler'));

        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . date('Y') . '-' . date('m') . '/';
        //$exception->info = $directory;
        
        //try {
            $directoryOk = true;
            if (!@is_dir($directory)) {            
                $directoryOk = false;
                $this->logger->info("creating Report Directory $directory");                
                if(!@mkdir($directory, 0777, TRUE)){
                    $this->logger->error("Cannot create directory '$directory'");
                }
            }
            
            if($directoryOk){                
                if(is_writable(SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT)){
                    $nameFile = $userName . '.xlsx';
                    $newFilePath = $directory . $nameFile;

                    if (file_exists($newFilePath)) {
                        $this->updateReportFile($lsReport, $userName);
                    } else {
                        $this->generateReportFile($lsReport, $newFilePath);
                    }
                }
                else{
                    $this->logger->error("Folder '".SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT."' not writeable.");
                }
            }
        //} catch (Throwable $e) {
            //throw $e;
        //}
    }
    /**
     * 
     * @param type $lsReport
     * @param type $newFilePath
     */
    public function updateReportFile($lsReport, $userName, $month = '', $year = '') {
        $monthDate = (!empty($month)) ? $month : date('m');
        $yearDate = (!empty($year)) ? $year : date('Y');
        
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $yearDate . '-' . $monthDate . '/';
        $fileName = $userName . '.xlsx';
        $existingFilePath = $directory . $fileName;

        // we need a reader to read the existing file...
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($existingFilePath);

        // ... and a writer to create the new file
        $writer = WriterFactory::create(Type::XLSX);
        
        if(!empty($month) && !empty($year)){
            $newFilePath = $directory . $userName . ".xlsx";
            $writer->openToBrowser($newFilePath);
        }else {
            $newFilePath = $directory . $userName . "_Include_SMS_awaiting_DR.xlsx";
            $writer->openToFile($newFilePath);
        }
        
        $totalCount = array_sum(array_column($lsReport, 'MESSAGE_COUNT'));
        $unchargedCount = array_sum(array_column($lsReport, 'UNDELIVERED_UNCHARGED'));
        $deliveredCount = array_sum(array_column($lsReport, 'DELIVERED'));
        $undeliveredCount = array_sum(array_column($lsReport, 'UNDELIVERED'));
        $totalCharged = (int) $undeliveredCount + (int)$deliveredCount;
        
        // let's read the entire spreadsheet...
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
            // Add sheets in the new file, as we read new sheets in the existing one
            if ($sheetIndex !== 1) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            foreach ($sheet->getRowIterator() as $row) {
                $keyName = $row[0];
                
                switch ($keyName){
                    case 'Generated Report On'          : $row[1]  = date("j F Y (H:i)"); break;
                    case 'DELIVERED SMS'                : $row[1] += $deliveredCount;      break;
                    case 'UNDELIVERED SMS (CHARGED)'    : $row[1] += $undeliveredCount;    break;
                    case 'UNDELIVERED SMS (NOT CHARGED)': $row[1] += $unchargedCount;      break;
                    case 'TOTAL SMS'                    : $row[1] += $totalCount;          break;
                    case 'TOTAL SMS CHARGED'            : $row[1] += $totalCharged;        break;
                }

                // ... and copy each row into the new spreadsheet
                $writer->addRow($row);
            }

            if (isset($lsReport[0]['MESSAGE_ID'])) {
                foreach ($lsReport as $rows) {
                    unset($rows['TSEL']);
                    unset($rows['NON_TSEL']);
                    unset($rows['DELIVERED']);
                    unset($rows['UNDELIVERED']);
                    unset($rows['UNDELIVERED_UNCHARGED']);
                    unset($rows['UNCHARGED']);
                    $writer->addRow($rows);
                }
            } 
        }
 
       $reader->close();
        $writer->close();

//        if ($existingFilePath !== $newFilePath){
//            if(!@unlink($existingFilePath)){ $this->logger->warn("Cannot remove $existingFilePath");}
//            else{ $this->logger->warn("remove file '$existingFilePath'");}
//        
//            if(!@rename($newFilePath, $existingFilePath)) {
//                $this->logger->warn("Cannot rename '$newFilePath' to '$existingFilePath'");
//            }
//            else{
//                $this->logger->warn("Rrename '$newFilePath' to $existingFilePath");
//            }
//        }
    }

    /**
     * 
     * @param type $lsReport
     * @param type $newFilePath
     */
    public function generateReportFile($lsReport, $newFilePath) {
        $style = (new StyleBuilder())
                ->setFontBold()
                ->build();

        $today = date("j F Y (H:i)");
        
        $totalCount       = array_sum(array_column($lsReport, 'MESSAGE_COUNT'));
        $deliveredCount   = array_sum(array_column($lsReport, 'DELIVERED'));
        $unchargedCount   = array_sum(array_column($lsReport, 'UNDELIVERED_UNCHARGED'));
        $undeliveredCount = array_sum(array_column($lsReport, 'UNDELIVERED'));
//        MESSAGE_COUNT,
//        IF(X.IS_RECREDITED = 1, MESSAGE_COUNT, 0) AS UNCHARGED,
//        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Delivered' ,   MESSAGE_COUNT, 0), 0) AS DELIVERED,
//        IF(X.IS_RECREDITED = 1, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED_UNCHARGED,
//        IF(X.IS_RECREDITED = 0, IF(X.STATUS  =  'Undelivered' , MESSAGE_COUNT, 0), 0) AS UNDELIVERED
        $totalCharged     = (int) $undeliveredCount + (int)$deliveredCount;

        // ... and a writer to create the new file
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($newFilePath);

        // At this point, the new spreadsheet contains the same data as the existing one.
        // Add the new data:

        $writer->addRow(['']);
        $writer->addRow(['']);
        $writer->addRowWithStyle(['Generated Report On',            $today],            $style);
        $writer->addRow(['']);
        $writer->addRowWithStyle(['DELIVERED SMS',                  $deliveredCount],   $style);
        $writer->addRowWithStyle(['UNDELIVERED SMS (CHARGED)',      $undeliveredCount], $style);
        $writer->addRowWithStyle(['UNDELIVERED SMS (NOT CHARGED)',  $unchargedCount],   $style);
        $writer->addRowWithStyle(['TOTAL SMS',                      $totalCount],       $style);
        $writer->addRowWithStyle(['TOTAL SMS CHARGED',              $totalCharged],     $style);
        $writer->addRow(['']);
        $writer->addRow(['']);



        $writer->addRowWithStyle(['MESSAGE ID', 'DESTINATION', 'MESSAGE CONTENT', 'ERROR CODE', 'DESCRIPTION CODE','SEND DATETIME', 'SENDER',
            'USER ID', 'MESSAGE COUNT'], $style);

        foreach ($lsReport as $rows) {
            unset($rows['TSEL']);
            unset($rows['NON_TSEL']);
            unset($rows['DELIVERED']);
            unset($rows['UNDELIVERED']);
            unset($rows['UNDELIVERED_UNCHARGED']);
            unset($rows['UNCHARGED']);
            $writer->addRow($rows);
        }

        $writer->close();
    }

    /**
     * 
     */
    public function downloadDataSpout($userName, $month, $year, $lsReport) {
        
        $this->updateReportFile($lsReport, $userName, $month, $year);
    }
    
    public function checkFile($userName, $month, $year){
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . "{$year}-{$month}/";
        $nameFile = $userName . '.xlsx';
        $existingFilePath = $directory . $nameFile;
        
        if(file_exists($existingFilePath)){
           return $this->getLastDateFromReport($userName, $month, $year);
        }else{
           return false;
        }
    }
    
    
    public function getLastDateFromReport($userName, $month, $year){
        $lastDate = "{$year}-{$month}-01 00:00:00";
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . "{$year}-{$month}/";
        $nameFile = $userName . '.xlsx';
        $existingFilePath = $directory . $nameFile;
        // we need a reader to read the existing file...
        
        if(is_readable($existingFilePath)){
            //try{
                $reader = ReaderFactory::create(Type::XLSX);
                $reader->open($existingFilePath);

                // let's read the entire spreadsheet...
                foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        if(isset($row[4])){
                            //echo $row[4]."\n";
                            if($row[4] != 'SEND DATETIME' && !empty($row[4])) {
                                $lastDate = $row[4];
                            }                
                        }
                    }
                }
                $reader->close();
            //}

            //catch (Throwable $e){
                //$this->logger->error($e->getMessage());
            //}
        }
        else{
            $this->logger->warn("file $existingFilePath not readable");
        }
        return $lastDate;
    }

    public function getLastRecordDateTime($nameFile, $month, $year) {
        $newFilePath = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . "{$year}-{$month}/{$nameFile}";
        header('Content-Description: File Transfer');
        header('Content-Type: application/xlsx');
        header('Content-Disposition: attachment; filename=' . basename($newFilePath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($newFilePath));
        readfile($newFilePath);
        //exit;
    }

}
