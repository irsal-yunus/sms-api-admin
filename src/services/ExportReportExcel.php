<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Fathir Wafda
 */
//call excelWriter
include_once __DIR__.'/../classes/PHPExcel.php';
include_once __DIR__.'/../classes/PHPExcel/IOFactory.php';
include __DIR__.'/../classes/PHPExcel/Writer/Excel2007.php';
include __DIR__.'/../classes/PHPExcel/ReferenceHelper.php';
require_once __DIR__.'/../configs/config.php';
require_once __DIR__.'/../init.d/init.php';

class ExportReportExcel extends ApiBaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function exportData($userId, $lsReport) {
//        var_dump($userId);
//        
//        var_dump($lsReport[0]);
//        

        $lastDate = date("Y-m", strtotime("-1 months"));

//Config file name
        $nameFile = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . $userId . $lastDate . '.xlsx';

//Config title data
//$user = "USER ID: ";
//Writing header
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $nameFile . "");
        header("Content-Transfer-Encoding: binary ");



        $objXls = new PHPExcel();

        $objSheet = $objXls->setActiveSheetIndex(0);

//$objSheet->setCellValue('A2', $user);
        $objSheet->setCellValue('A3', "TOTAL SMS:")->getStyle("A3")->getFont()->setBold(true);
        $objSheet->setCellValue('A4', "DELIVERED:")->getStyle("A4")->getFont()->setBold(true);
        $objSheet->setCellValue('A5', "UNDELIVERED:")->getStyle("A5")->getFont()->setBold(true);
        $objSheet->setCellValue('A6', "PENDING:")->getStyle("A6")->getFont()->setBold(true);
        $objSheet->setCellValue('A7', "UNKNOWN:")->getStyle("A7")->getFont()->setBold(true);
        $objSheet->setCellValue('A8', "TELKOMSEL:")->getStyle("A8")->getFont()->setBold(true);
        $objSheet->setCellValue('A9', "NON TELKOMSEL:")->getStyle("A9")->getFont()->setBold(true);
//$objSheet->setCellValue('B2', $userId);
//$objSheet->setCellValue('E39', '=COUNTIFS(\'Backup Data\'!$H:$H,"=KTM")');


        $objSheet->setCellValue('A12', 'MESSAGE ID')->getStyle("A12")->getFont()->setBold(true);
        $objSheet->setCellValue('B12', 'DESTINATION')->getStyle("B12")->getFont()->setBold(true);
        $objSheet->setCellValue('C12', 'MESSAGE CONTENT')->getStyle("C12")->getFont()->setBold(true);
        $objSheet->setCellValue('D12', 'MESSAGE STATUS')->getStyle("D12")->getFont()->setBold(true);
        $objSheet->setCellValue('E12', 'SEND DATETIME')->getStyle("E12")->getFont()->setBold(true);
        $objSheet->setCellValue('F12', 'SENDER')->getStyle("F12")->getFont()->setBold(true);
        $objSheet->setCellValue('G12', 'USER ID')->getStyle("G12")->getFont()->setBold(true);
        $objSheet->setCellValue('H12', 'MESSAGE COUNT')->getStyle("H12")->getFont()->setBold(true);
        $objXls->getActiveSheet()->setTitle("SMS Billing");

        $counter = 13;
        foreach ($lsReport as $row) {
            $objSheet->setCellValue('A' . $counter, $row["MESSAGE_ID"]);
            $objSheet->setCellValue('B' . $counter, $row["DESTINATION"]);
            $objSheet->setCellValue('C' . $counter, $row["MESSAGE_CONTENT"]);
            $objSheet->setCellValue('D' . $counter, $row["MESSAGE_STATUS"]);
            $objSheet->setCellValue('E' . $counter, $row["SEND_DATETIME"]);
            $objSheet->setCellValue('F' . $counter, $row["SENDER"]);
            $objSheet->setCellValue('G' . $counter, $row["USER_ID"]);
            $objSheet->setCellValue('H' . $counter, $row["MESSAGE_COUNT"]);
            $counter++;
        }

        $objSheet->setCellValue('B3', '=SUM(H13:H' . $counter . ')')->getStyle('B3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        ;
        $objSheet->setCellValue('B4', '=SUMIF(D13:D' . $counter . ',"Delivered",H13:H' . $counter . ')')->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        ;
        $objSheet->setCellValue('B5', '=SUMIF(D13:D' . $counter . ',"Undelivered",H13:H' . $counter . ')')->getStyle('B5')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        ;
        $objSheet->setCellValue('B6', '=SUMIF(D13:D' . $counter . ',"Pending",H13:H' . $counter . ')')->getStyle('B6')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        ;
        $objSheet->setCellValue('B7', '=SUMIF(D13:D' . $counter . ',"Unknown",H13:H' . $counter . ')')->getStyle('B7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        ;
//$objSheet->setCellValue('B8','=COUNTIF(B13:B'.$counter.',"62811.*")+COUNTIF(B13:B'.$counter.',"62812.*")+COUNTIF(B13:B'.$counter.',"62813.*")+COUNTIF(B13:B'.$counter.',"62821.*")+COUNTIF(B13:B'.$counter.',"62822.*")+COUNTIF(B13:B'.$counter.',"62823.*")+COUNTIF(B13:B'.$counter.',"62851.*")+COUNTIF(B13:B'.$counter.',"62852.*")+COUNTIF(B13:B'.$counter.',"62853.*")')->getStyle('B8')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);;
        $objSheet->setCellValue('B8', '=SUMIF(B13:B' . $counter . ',"62811.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62812.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62813.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62821.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62822.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62823.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62851.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62852.*",H13:H' . $counter . ')+SUMIF(B13:B' . $counter . ',"62853.*",H13:H' . $counter . ')')->getStyle('B8')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        ;
        $objSheet->setCellValue('B9', '=SUM(B3-B8)')->getStyle('B9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        ;


        $objXls->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);

        $objXls->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objXls, 'Excel2007');
//$objWriter->save("/home/fathir/Downloads/.$namaFile");
//$objWriter->setIncludeCharts(TRUE);
//ob_end_clean();
//$objWriter->save(str_replace(__DIR__,"/home/fathir/Documents/SMS_BILLING/.$namaFile",__DIR__));
//$objWriter->save('php://output/home/fathir/Documents/SMS_BILLING/');

        $objWriter->save($nameFile);
    }

    /**
     * Download report excel.
     * @param int $userName
     */
    public function downloadData($userName, $lsReport)
    {
        $lastDate = date("Y-m-d");

//Config file name
        $nameFile = $userName . $lastDate . '.xlsx';

//Config title data
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=" . $nameFile . "");
        header("Content-Transfer-Encoding: binary ");



        $objXls = new PHPExcel();

        $objSheet = $objXls->setActiveSheetIndex(0);

        $objSheet->setCellValue('A3', "TOTAL SMS:")->getStyle("A3")->getFont()->setBold(true);
        $objSheet->setCellValue('A4', "DELIVERED:")->getStyle("A4")->getFont()->setBold(true);
        $objSheet->setCellValue('A5', "UNDELIVERED:")->getStyle("A5")->getFont()->setBold(true);
        $objSheet->setCellValue('A6', "PENDING:")->getStyle("A6")->getFont()->setBold(true);
        $objSheet->setCellValue('A7', "UNKNOWN:")->getStyle("A7")->getFont()->setBold(true);
        $objSheet->setCellValue('A8', "TELKOMSEL:")->getStyle("A8")->getFont()->setBold(true);
        $objSheet->setCellValue('A9', "NON TELKOMSEL:")->getStyle("A9")->getFont()->setBold(true);


        $objSheet->setCellValue('A12', 'MESSAGE ID')->getStyle("A12")->getFont()->setBold(true);
        $objSheet->setCellValue('B12', 'DESTINATION')->getStyle("B12")->getFont()->setBold(true);
        $objSheet->setCellValue('C12', 'MESSAGE CONTENT')->getStyle("C12")->getFont()->setBold(true);
        $objSheet->setCellValue('D12', 'MESSAGE STATUS')->getStyle("D12")->getFont()->setBold(true);
        $objSheet->setCellValue('E12', 'ERROR CODE')->getStyle("E12")->getFont()->setBold(true);
        $objSheet->setCellValue('F12', 'SEND DATETIME')->getStyle("F12")->getFont()->setBold(true);
        $objSheet->setCellValue('G12', 'SENDER')->getStyle("G12")->getFont()->setBold(true);
        $objSheet->setCellValue('H12', 'USER ID')->getStyle("H12")->getFont()->setBold(true);
        $objSheet->setCellValue('I12', 'MESSAGE COUNT')->getStyle("I12")->getFont()->setBold(true);
        $objXls->getActiveSheet()->setTitle("SMS Billing");

        $counter = 13;
//        $objXls->getActiveSheet()->fromArray(
//            $lsReport,
//            NULL,
//            'A' . $counter
//        );
        foreach ($lsReport as $row) {
            $objSheet->setCellValue('A' . $counter, $row["MESSAGE_ID"]);
            $objSheet->setCellValue('B' . $counter, $row["DESTINATION"]);
            $objSheet->setCellValue('C' . $counter, $row["MESSAGE_CONTENT"]);
            $objSheet->setCellValue('D' . $counter, $row["STATUS"]);
            $objSheet->setCellValue('E' . $counter, $row["MESSAGE_STATUS"]);
            $objSheet->setCellValue('F' . $counter, $row["SEND_DATETIME"]);
            $objSheet->setCellValue('G' . $counter, $row["SENDER"]);
            $objSheet->setCellValue('H' . $counter, $row["USER_ID"]);
            $objSheet->setCellValue('I' . $counter, $row["MESSAGE_COUNT"]);
            $counter++;
        }

        $objSheet->setCellValue('B3', '=SUM(I13:I' . $counter . ')')->getStyle('B3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        
        $objSheet->setCellValue('B4', '=SUMIF(D13:D' . $counter . ',"Delivered",I13:I' . $counter . ')')->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        
        $objSheet->setCellValue('B5', '=SUMIF(D13:D' . $counter . ',"Undelivered",I13:I' . $counter . ')')->getStyle('B5')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        
        $objSheet->setCellValue('B6', '=SUMIF(D13:D' . $counter . ',"Pending",I13:I' . $counter . ')')->getStyle('B6')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        
        $objSheet->setCellValue('B7', '=SUMIF(D13:D' . $counter . ',"Unknown",I13:I' . $counter . ')')->getStyle('B7')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);

        $objSheet->setCellValue('B8', '=SUMIF(B13:B' . $counter . ',"62811.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62812.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62813.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62821.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62822.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62823.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62851.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62852.*",I13:I' . $counter . ')+SUMIF(B13:B' . $counter . ',"62853.*",I13:I' . $counter . ')')->getStyle('B8')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        
        $objSheet->setCellValue('B9', '=SUM(B3-B8)')->getStyle('B9')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
        
        $objXls->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
        $objXls->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

        $objXls->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objXls, 'Excel2007');

        $objWriter->save('php://output');
    }
}
