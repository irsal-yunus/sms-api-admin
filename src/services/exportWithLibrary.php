<?php 
//memanggil fungsi excelWriter
include_once '../classes/PHPExcel.php';
require_once '../configs/config.php';
 
//koneksi ke database dan jalankan query
//mysql_connect(REF_DB_HOST, REF_DB_USER, REF_DB_PASSWORD);
//mysql_select_db(REF_DB_NAME);
//$result = mysql_query("SELECT * FROM USER_MESSAGE_STATUS");
//!$result?die(mysql_error()):'';
 
mysql_connect('localhost', 'root', '#1rstwap');
mysql_select_db('SMPP');
$result = mysql_query("SELECT * FROM CDR201411");
!$result?die(mysql_error()):'';

//pengaturan nama file
$namaFile = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT.'tes_smsapiadmin.xls';
//pengaturan judul data
$judul = "Data KISEL";
 
//penulisan header
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=".$namaFile."");
header("Content-Transfer-Encoding: binary ");
 
 

$objXls = new PHPExcel();

$objSheet = $objXls->setActiveSheetIndex(0);

$objSheet->setCellValue('A2', $judul);
//$objSheet->setCellValue('A4', 'MESSAGE ID');
//$objSheet->setCellValue('B4', 'DESTINATION');
//$objSheet->setCellValue('C4', 'MESSAGE CONTENT');
//$objSheet->setCellValue('D4', 'MESSAGE STATUS');
//$objSheet->setCellValue('E4', 'SEND DATETIME');
//$objSheet->setCellValue('F4', 'SENDER');
//$objSheet->setCellValue('G4', 'USER ID');

$objSheet->setCellValue('A4', 'MESSAGE ID');
$objSheet->setCellValue('B4', 'DESTINATION');
$objSheet->setCellValue('C4', 'MESSAGE CONTENT');
$objSheet->setCellValue('D4', 'MESSAGE STATUS');
$objSheet->setCellValue('E4', 'SEND DATETIME');
$objSheet->setCellValue('F4', 'SENDER');
$objSheet->setCellValue('G4', 'USER ID');
$objSheet->setCellValue('H4', 'MESSAGE COUNT');
$objSheet->setCellValue('I4', 'ErrorCode');
$objSheet->setCellValue('J4', 'ServerName');

$objXls->getActiveSheet()->setTitle("Delivery Status");

$counter = 4;
//while ($data = mysql_fetch_array($result))
while ($row = mysql_fetch_array($result))
{

$counter++;

//    $objSheet->setCellValue('A' . $counter, $row["MESSAGE_ID"]);
//    $objSheet->setCellValue('B' . $counter, $row["DESTINATION"]);
//    $objSheet->setCellValue('C' . $counter, $row["MESSAGE_CONTENT"]);
//    $objSheet->setCellValue('D' . $counter, $row["MESSAGE_STATUS"]);
//    $objSheet->setCellValue('E' . $counter, $row["SEND_DATETIME"]);
//    $objSheet->setCellValue('F' . $counter, $row["SENDER"]);
//    $objSheet->setCellValue('G' . $counter, $row["USER_ID"]);

 $objSheet->setCellValue('A' . $counter, $row["ID"]);
    $objSheet->setCellValue('B' . $counter, $row["SubmitDateTime"]);
    $objSheet->setCellValue('C' . $counter, $row["DateTime"]);
    $objSheet->setCellValue('D' . $counter, $row["QueueMessageID"]);
    $objSheet->setCellValue('E' . $counter, $row["SMPPMessageID"]);
    $objSheet->setCellValue('F' . $counter, $row["DestinationNumber"]);
    $objSheet->setCellValue('G' . $counter, $row["OriginateNumber"]);
    $objSheet->setCellValue('H' . $counter, $row["DeliveryStatus"]);
    $objSheet->setCellValue('I' . $counter, $row["ErrorCode"]);
    $objSheet->setCellValue('J' . $counter, $row["ServerName"]);
 
}

//$objXls->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
//$objXls->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
//$objXls->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
//$objXls->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
//$objXls->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
//$objXls->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
//$objXls->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

$objXls->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
$objXls->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);

$objXls->setActiveSheetIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter($objXls,'Excel5');
//$objWriter->save("/home/fathir/Downloads/.$namaFile");
$objWriter->setIncludeCharts(TRUE);
//ob_end_clean();
//$objWriter->save(str_replace(__DIR__,"/home/fathir/Documents/SMS_BILLING/.$namaFile",__DIR__));
//$objWriter->save('php://output/home/fathir/Documents/SMS_BILLING/');
$objWriter->save($namaFile);

//mysql_free_stmt($stmt);
//mysql_close($conn);
exit();
?>