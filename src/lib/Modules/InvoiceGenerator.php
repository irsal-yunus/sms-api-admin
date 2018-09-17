<?php

namespace Firstwap\SmsApiAdmin\lib\Modules;

use Firstwap\SmsApiAdmin\lib\model\InvoiceHistory;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
use Firstwap\SmsApiAdmin\lib\model\InvoiceSetting;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class InvoiceGenerator
{
    /**
     * Log4php instance
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor for ApiInvoiceSetting class
     *
     * @return void
     */
    public function __construct()
    {
        $this->logger = \Logger::getLogger("service");
    }

    /**
     * Generate invoice for each month
     *
     * @return void
     */
    public function generate()
    {
        $setting = $this->getSetting();
        $profiles = $this->getProfiles();

        if (empty($profiles)) {
            // Logger
            $this->logger->info('NO Profile that should generate in this month or the invoices already generated for this month');
            echo "\033[1;31mNo Profiles\n\033[1;37m";
            // End Logger

            return;
        }

        $setting->refreshInvoiceNumber();

        // Logger
        $maxLength = max(array_map('strlen', array_column($profiles, 'companyName')));
        // End Logger

        foreach ($profiles as $profile) {
            // Logger
            $repeat = str_repeat(' ', $maxLength - strlen($profile->companyName));
            echo "\033[1;34m$profile->companyName$repeat\t\t\t";
            // End Logger

            try {
                $profile->beginTransaction();

                $invoice = $this->insertInvoice($setting, $profile);

                foreach ($profile->products as $product) {
                    $product->profile2History($invoice, $invoice->key());
                }

                $this->createPdfFile($invoice, $profile, $setting);

                $profile->commit();

                // Logger
                $this->logger->info('Success Invoice for '. $profile->companyName);
                echo "\033[1;32mSuccess\n\033[1;37m";
                // End Logger
            } catch (\Exception $e) {
                $profile->rollback();
                $invoice->deleteInvoiceFile();
                $setting->refreshInvoiceNumber();

                // Logger
                $this->logger->error('Failed Invoice for '. $profile->companyName);
                $this->logger->error($e->getMessage());
                $this->logger->error($e->getTraceAsString());
                echo "\033[1;31mFailed\n\033[1;37m";
                // End Logger
            }
        }

        $setting->refreshInvoiceNumber();
    }

    /**
     * Insert Invoice based on profile
     *
     * @param InvoiceSetting $settings
     * @param InvoiceProfile $profile
     * @return void
     */
    protected function insertInvoice(InvoiceSetting &$settings, InvoiceProfile &$profile)
    {
        $settings->lastInvoiceNumber = intval($settings->lastInvoiceNumber) + 1;

        $attributes = [
            'profileId'     => $profile->key(),
            'invoiceNumber' => $settings->lastInvoiceNumber,
            'startDate'     => date('Y-m-d'),
            'dueDate'       => date('Y-m-d', strtotime("{$settings->paymentPeriod} days")),
            'refNumber'     => '',
            'invoiceType'   => InvoiceHistory::ORIGINAL,
            'status'        => InvoiceHistory::INVOICE_UNLOCK,
        ];

        $history           = $this->history($attributes);
        $history->fileName = $this->getFolderPath($history).$this->generateFileName($history, $profile);

        $history->save();

        return $history;
    }

    /**
     * Create invoice PDF File
     *
     * @param  InvoiceHistory $invoice
     * @param  InvoiceProfile $profile
     * @param  InvoiceSetting $setting
     * @return string
     */
    public function createPdfFile(InvoiceHistory $invoice, InvoiceProfile $profile, InvoiceSetting $setting)
    {
        $invoice->loadProduct();

        $folderPath     = $this->getFolderPath($invoice);
        $fileName       = $folderPath.$this->generateFileName($invoice, $profile);
        $page           = \SmsApiAdmin::getTemplate();

        $page->assign('profile', $profile);
        $page->assign('setting', $setting);
        $page->assign('invoice', $invoice);
        $page->setTemplateDir(SMSAPIADMIN_TEMPLATE_DIR . "/pdf");

        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 45,
            'margin_bottom' => 0,
        ]);

        $header = $page->fetch('invoice.header.tpl');
        $content = $page->fetch('invoice.content.tpl');

        $mpdf->SetHTMLHeader($header);
        $mpdf->WriteHTML($content);
        $mpdf->Output(SMSAPIADMIN_INVOICE_DIR.$fileName, Destination::FILE);

        return $fileName;
    }

    /**
     * Invoice file name
     *
     * @param InvoiceHistory $invoice
     * @param InvoiceProfile $profile
     * @return  String
     */
    public function generateFileName(InvoiceHistory $invoice, InvoiceProfile $profile)
    {
        $startDate  = strtotime($invoice->startDate);
        $fullMonth  = date('F', $startDate);
        $clientName = str_replace(" ", "_", $profile->companyName);
        $status     = $invoice->isLock() ? "_FINAL" : "_PREVIEW";

        return "{$invoice->invoiceNumber}_{$clientName}_{$fullMonth}_{$invoice->invoiceType}$status.pdf";
    }

    /**
     * Create invoice folder
     *
     * @param InvoiceHistory $invoice
     * @return string
     */
    protected function getFolderPath(InvoiceHistory $invoice)
    {
        $startDate  = strtotime($invoice->startDate);
        $year       = date('Y', $startDate);
        $month      = date('m', $startDate);
        $folderPath = "$year/$month/";

        if (!file_exists(SMSAPIADMIN_INVOICE_DIR.$folderPath)) {
            mkdir(SMSAPIADMIN_INVOICE_DIR.$folderPath, 0777, true);
        }

        return $folderPath;
    }

    // public function syncInvoiceFiles()
    // {
    //     // rsync -azP -e "ssh -i $HOME/.ssh/id_rsa" /var/www/html/admin/src/archive/invoices/ dev@10.32.6.61:/home/dev/sync/
    //     $command        = "rsync -azP -e \"ssh -i ".INVOICE_SSH_PRIVATE_KEY."\" ";
    //     $sourcePath     = SMSAPIADMIN_INVOICE_DIR;
    //     $destination    = INVOICE_SHARED_FOLDER_USER."@".INVOICE_SHARED_FOLDER_IP.":".INVOICE_SHARED_FOLDER_PATH;
    //     $syncFinal      = $command." --delete --include=\"*_FINAL.pdf\" ".$sourcePath." ".$destination;
    //     $syncPreview    = $command." --include=\"*_PREVIEW.pdf\" ".$sourcePath." ".$destination;
    //     // echo $syncFinal."\n";
    //     // echo $syncPreview."\n";
    //     $this->logger->info('syncInvoiceFiles : '. $syncFinal);
    //     $this->logger->info('syncInvoiceFiles : '. $syncPreview);
    //     exec($syncFinal.";".$syncPreview, $output);

    //     $this->logger->info('syncInvoiceFiles : '. \json_encode($output));
    // }

    /**
     * Initial InvoiceHistory instance
     *
     * @param Array $attributes
     * @return InvoiceHistory
     */
    protected function history(array $attributes = [])
    {
        return new InvoiceHistory($attributes);
    }

    /**
     * Get Profiles that will be generate
     * Profile data will include profile's products
     *
     * @return array
     */
    protected function getProfiles()
    {
        return (new InvoiceProfile)->getProfileForAutoGenerate();
    }

    /**
     * Get invoice settings
     *
     * @return InvoiceSetting
     */
    protected function getSetting()
    {
        return (new InvoiceSetting())->getSetting();
    }
}
