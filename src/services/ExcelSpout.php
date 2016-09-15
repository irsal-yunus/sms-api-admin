<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * @author Fathir Wafda
 * 
 */


require_once '../classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once '../configs/config.php';
require_once '../init.d/init.php';

//use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;

class ExcelSpout extends ApiBaseModel {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @param type $userName
     * @param type $lsReport
     */
    public function getDataScheduled($userName, $lsReport) {
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . '' . date('Y') . '-' . date('m') . '/';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, TRUE);
        }

        $nameFile = $userName . '.xlsx';
        $newFilePath = $directory . $nameFile;

        if (file_exists($newFilePath)) {
            $this->updateReportFile($lsReport, $userName);
        } else {
            $this->generateReportFile($lsReport, $newFilePath);
        }
    }

    /**
     * 
     * @param type $lsReport
     * @param type $newFilePath
     */
    public function updateReportFile($lsReport, $userName, $month = '', $year = '') {
        $monthDate = (!empty($month)) ? $month : date('m');
        $yearDate = (!empty($year)) ? $year : date('Y');
        
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . '' . $yearDate . '-' . $monthDate . '/';
        $nameFile = $userName . '.xlsx';
        $existingFilePath = $directory . $nameFile;

        // we need a reader to read the existing file...
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($existingFilePath);

        // ... and a writer to create the new file
        $writer = WriterFactory::create(Type::XLSX);
        
        if(!empty($month) && !empty($year)){
            $newFilePath = $directory . $userName . ".xlsx";
            $writer->openToBrowser($newFilePath);
        }else {
            $newFilePath = $directory . $userName . "2.xlsx";
            $writer->openToFile($newFilePath);
        }
        
        $totalCount = array_sum(array_column($lsReport, 'MESSAGE_COUNT'));
        $unchargedCount = array_sum(array_column($lsReport, 'UNCHARGED'));
        $deliveredCount = array_sum(array_column($lsReport, 'DELIVERED'));
        $undeliveredCount = array_sum(array_column($lsReport, 'UNDELIVERED'));
        
        // let's read the entire spreadsheet...
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
            // Add sheets in the new file, as we read new sheets in the existing one
            if ($sheetIndex !== 1) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            foreach ($sheet->getRowIterator() as $row) {
                $keyName = $row[0];
                
                if($keyName === 'TOTAL SMS:') {
                    $row[1] += $totalCount;
                } else if($keyName === 'DELIVERED:') {
                    $row[1] += $deliveredCount;
                } else if($keyName === 'UNDELIVERED:') {
                    $row[1] += $undeliveredCount;
                } else if($keyName === 'UNCHARGED:') {
                    $row[1] += $unchargedCount;
                }
                
                // ... and copy each row into the new spreadsheet
                $writer->addRow($row);
            }

            if (isset($lsReport[0]['MESSAGE_ID'])) {
                foreach ($lsReport as $rows) {
                    unset($rows['TSEL']);
                    unset($rows['NON_TSEL']);
                    unset($rows['UNCHARGED']);
                    unset($rows['DELIVERED']);
                    unset($rows['UNDELIVERED']);
                    $writer->addRow($rows);
                }
            } 
        }

        $reader->close();
        $writer->close();
        
        unlink($existingFilePath);
        rename($newFilePath, $existingFilePath);
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

        $totalCount = array_sum(array_column($lsReport, 'MESSAGE_COUNT'));
        $unchargedCount = array_sum(array_column($lsReport, 'UNCHARGED'));
        $deliveredCount = array_sum(array_column($lsReport, 'DELIVERED'));
        $undeliveredCount = array_sum(array_column($lsReport, 'UNDELIVERED'));

        // ... and a writer to create the new file
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($newFilePath);

        // At this point, the new spreadsheet contains the same data as the existing one.
        // Add the new data:

        $writer->addRow(['']);
        $writer->addRow(['']);
        $writer->addRowWithStyle(['TOTAL SMS:', $totalCount], $style);
        $writer->addRowWithStyle(['DELIVERED:', $deliveredCount], $style);
        $writer->addRowWithStyle(['UNDELIVERED:', $undeliveredCount], $style);
        $writer->addRowWithStyle(['UNCHARGED:', $unchargedCount], $style);
        $writer->addRow(['']);
        $writer->addRow(['']);


        $writer->addRowWithStyle(['MESSAGE ID', 'DESTINATION', 'MESSAGE CONTENT', 'ERROR CODE', 'SEND DATETIME', 'SENDER',
            'USER ID', 'MESSAGE COUNT'], $style);

        foreach ($lsReport as $rows) {
            unset($rows['TSEL']);
            unset($rows['NON_TSEL']);
            unset($rows['UNCHARGED']);
            unset($rows['DELIVERED']);
            unset($rows['UNDELIVERED']);

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
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($existingFilePath);
        
        // let's read the entire spreadsheet...
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $lastDate = $row[4];
            }
        }
        $reader->close();
        
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
        exit;
    }

}
