<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * @author Fathir Wafda
 * @author Basri Yasin
 */


require_once __DIR__.'/../classes/spout-2.5.0/src/Spout/Autoloader/autoload.php';
require_once __DIR__.'/../configs/config.php';
require_once __DIR__.'/../init.d/init.php';

//use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;

class ExcelSpout extends ApiBaseModel {
    
    public  $logger;
    public $currentUser, $currentMonth, $currentYear;
    
    public function __construct() {
        parent::__construct();
        $this->logger = Logger::getRootLogger();
    }

    /*
     * @param string $dir
     * @param string $
     * 
     */
    public function prepareDirectory($dir){
        $dirOk  = true;
        if (!@is_dir($dir)) {            
            $this->logger->info("Creating report directory $dir");                
            if(!@mkdir($dir, 0777, TRUE)){
                $this->logger->error("Could not create report directory '$dir'");
                $dirOk = false;                    
            }
        }
        
        return $dirOk;
    }

    public function prepareFile($dir, $file){
        $fileOk = true;
        if(is_writable($dir)){            
            if(file_exists($file)){
//                if(!is_writable($file)){
//                    $fileOk = false;                
//                    $this->logger->warn("File '$file' is not writeable.");
//                }
                if(!is_readable($file)){
                    $fileOk = false;
                    $this->logger->warn("File '$file' is not readable.");
                }
            }
            else{
                $this->logger->info("File '$file' not exist. Creating new Report");
            }
        }
        else{
            $fileOk = false;
            $this->logger->error("Directory '$dir' is no writeable.");
        }
        return $fileOk;
    }
    
     /**
     * 
     * @param string $userName
     * @param array  $lsReport
     */
    public function getDataScheduled($userName, $lsReport, $month=false, $year=false) {
        
        echo json_encode($lsReport, 192);
        die();
        $this->currentUser  = $userName;
        $this->currentMonth = $month !== false ? $month : date('m');
        $this->currentYear  = $year  !== false ? $year  : date('Y');
        //$directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . date('Y') . '-' . date('m') . '/';
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $this->currentYear . '-' . $this->currentMonth . '/';
        
        try {
            if($this->prepareDirectory($directory)){
                if(is_writable(SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT)){
                    $fileName = $directory.$userName.'.xlsx';
                    $rowCount = count($lsReport);
                    if($rowCount > 0 ){
                        if (file_exists($fileName)) {
                                $this->updateReportFile($lsReport, $userName, $month, $year);
                        } else {
                            $this->generateReportFile($lsReport, $fileName);
                        }                
                    }
                }
                else{
                    $this->logger->error("Folder '".SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT."' not writeable, please check the write permission.");
                }
            }
        } catch (Throwable $e) {
            $this->logger->error('getDataScheduled Error: '.$e->getMessage());
        }
    }
    /**
     * 
     * @param type $lsReport
     * @param type $newFilePath
     */
    public function updateReportFile($lsReport, $userName, $month = false, $year = false, $sms_dr = false) {
        $month = $month !== false ? $month : date('m');
        $year  = $year  !== false ? $year  : date('Y');
        
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . $year. '-' . $month. '/';
        $existingFilePath = $directory . $userName . '.xlsx';

        if($this->prepareFile($directory, $existingFilePath)){
            try{

                // we need a reader to read the existing file...
                $reader = ReaderFactory::create(Type::XLSX);
                $reader->open($existingFilePath);

                // ... and a writer to create the new file
                $writer = WriterFactory::create(Type::XLSX);

                //if(!empty($month) && !empty($year)){
                if(!$sms_dr){
                    $newFilePath = $directory . $userName . "_update_tmp.xlsx";
                    $writer->openToFile($newFilePath);
                }
                else {
                    if(!is_readable($existingFilePath)){
                        $this->logger->warn("Could not include SMS_DR File '$existingFilePath' is not writeable, please check the permission.");
                        $this->getLastRecordDateTime($fileName, $month, $year);
                    }
                    $newFilePath = $directory . $userName . "_Include_SMS_awaiting_DR.xlsx";
                    $writer->openToBrowser($newFilePath);
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

                    $style = (new StyleBuilder())
                            ->setFontBold()
                            ->build();
                    $i = 0;
                    foreach ($sheet->getRowIterator() as $row ) {
                        $keyName = $row[0];
                        $i++;
                        switch ($keyName){
                            case 'Generated Report On'          : $row[1]  = date("j F Y (H:i)"); break;
                            case 'DELIVERED SMS'                : $row[1] += $deliveredCount;      break;
                            case 'UNDELIVERED SMS (CHARGED)'    : $row[1] += $undeliveredCount;    break;
                            case 'UNDELIVERED SMS (NOT CHARGED)': $row[1] += $unchargedCount;      break;
                            case 'TOTAL SMS'                    : $row[1] += $totalCount;          break;
                            case 'TOTAL SMS CHARGED'            : $row[1] += $totalCharged;        break;
                        }

                        // ... and copy each row into the new spreadsheet
                        if($i > 12){
                            $writer->addRow($row);                    
                        }
                        else{
                            $writer->addRowWithStyle($row, $style);                    
                        }
                    }

                    if (isset($lsReport[0]['MESSAGE_ID'])) {
                        foreach ($lsReport as $rows) {
                            $rows["MESSAGE_COUNT"] = (int)$rows["MESSAGE_COUNT"];
                            unset($rows['DELIVERED']);
                            unset($rows['UNDELIVERED']);
                            unset($rows['UNDELIVERED_UNCHARGED']);
                            $writer->addRow($rows);
                        }
                    } 
                }
                $writer->close();
                $reader->close();
            }
            catch (Exception $e){
                $this->logger->error($e->getMessage());
            }

            if ($existingFilePath !== $newFilePath && !$sms_dr){
                if(!@unlink($existingFilePath)){ $this->logger->warn("Cannot remove $existingFilePath, please check the permission.");}
                //else{ $this->logger->info("remove file '$existingFilePath'");}

                if(!@rename($newFilePath, $existingFilePath)) {
                    $this->logger->warn("Cannot rename '$newFilePath' to '$existingFilePath', please check the permission.");
                }
                else{$this->logger->info("$year-$month Report for user $userName updated +".count($lsReport)." row(s).");}
            }
        }
        else {
            $this->logger->error("Prepare file failed '$existingFilePath'.");
        }
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
        $totalCharged     = (int) $undeliveredCount + (int)$deliveredCount;

        try{
            $writer = WriterFactory::create(Type::XLSX);
            $writer->openToFile($newFilePath);

            // Report Description
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

            // Table Header
            $writer->addRowWithStyle(['MESSAGE ID', 'DESTINATION', 'MESSAGE CONTENT', 'ERROR CODE', 'DESCRIPTION CODE','SEND DATETIME', 'SENDER', 'USER ID', 'MESSAGE COUNT'], $style);
            
            foreach ($lsReport as $rows) {
                $rows["MESSAGE_COUNT"] = (int)$rows["MESSAGE_COUNT"];
                unset($rows['DELIVERED']);
                unset($rows['UNDELIVERED']);
                unset($rows['UNDELIVERED_UNCHARGED']);
                $writer->addRow($rows);
            }

            $writer->close();
            $this->logger->info($this->currentYear.'-'.$this->currentMonth.' Report for user '.$this->currentUser.' created with '.count($lsReport).' row(s).');
        }
        catch (Exception $e){
            $this->logger->error("generateReportFile Error :". $e->getMessage());
        }
    }

    /**
     * 
     */
    public function downloadDataSpout($userName, $month, $year, $lsReport) {
        $this->logger->info('Create Report with Include_SMS_awaiting_DR for user '.$userName);
        $this->updateReportFile($lsReport, $userName, $month, $year, true);
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
        $lastDate  = "$year-$month-01";// 00:00:00";
        $directory = SMSAPIADMIN_ARCHIEVE_EXCEL_SPOUT . "$year-$month";
        //$nameFile  = $userName . '.xlsx';
        $fileName  = "$directory/$userName.xlsx";//$directory . $nameFile;
        // we need a reader to read the existing file...
        if(file_exists($fileName)){
            if(is_readable($fileName)){
                try{
                    $reader = ReaderFactory::create(Type::XLSX);
                    $reader->open($fileName);

                    // let's read the entire spreadsheet...
                    foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                        foreach ($sheet->getRowIterator() as $row) {
                            if(isset($row[5])){
                                if($row[5] != 'SEND DATETIME' && !empty($row[5])) {
                                    $lastDate = $row[5];
                                }                
                            }
                        }
                    }
                    $reader->close();
                }

                catch (Throwable $e){
                    $this->logger->error('getLastDateFromReport error: ',$e->getMessage());
                }
            }
            else{
                $this->logger->warn("file '$fileName' not readable, please check the read permission.");
            }
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
