<?php
/*
 * Copyright(c) 2018 1rstWAP. All rights reserved.
 */
require_once '../../vendor/autoload.php';

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;

$logger = Logger::getRootLogger();
SmsApiAdmin::filterAccess();
$service = new AppJsonService();
$invoiceModel = new InvoiceHistory();

try {
    $errorFields = [];
    $definitions = [
        "period" => FILTER_SANITIZE_NUMBER_INT,
    ];
    $newData = filter_input_array(INPUT_POST, $definitions);

    if (empty($newData['period'])) {
        $errorFields['period'] = 'Invoice Date should not be empty!';
    }

    $invoices = $invoiceModel->whereStartDate($newData['period']);

    if (empty($invoices)) {
        http_response_code(404);
        echo "No Invoice File";
        exit();
    }

    $zipTemp = SMSAPIADMIN_INVOICE_DIR . '/all.zip';
    $zip = new ZipArchive;

    if (file_exists($zipTemp)) {
        unlink($zipTemp);
    }

    if ($zip->open($zipTemp, ZipArchive::CREATE) !== true) {
        echo "can't create zip file";
        exit();
    }

    $baseName = date('Y-m', $newData['period']);

    foreach ($invoices as $invoice) {
        if (!$invoice->fileExists() && !$invoice->isLock()) {
            $invoice->createInvoiceFile();
        }

        if ($invoice->fileExists()) {
            $zip->addFile($invoice->filePath(), "{$baseName}/{$invoice->fileName}");
        }
    }

    $zip->close();

    ob_start();
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: public');
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Invoice-' . $baseName . '.zip"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($zipTemp));
    ob_end_flush();
    @readfile($zipTemp);

} catch (Exception $e) {
    $logger->error("$e");
    $service->setStatus(false);
    $service->summarise($e->getMessage());
    $service->deliver();
}
