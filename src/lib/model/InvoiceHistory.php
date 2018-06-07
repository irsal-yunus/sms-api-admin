<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use \DateTime;
use \Exception;
use \NumberFormatter;

/**
 * Model for INVOICE_HISTORY table
 * This model use to get and update History for invoice
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InvoiceHistory extends ModelContract
{

    /**
     * Constant Invoice Status
     */
    const INVOICE_UNLOCK = 0;
    const INVOICE_LOCK = 1;

    /**
     * Table name of invoice history
     *
     * @var string
     */
    protected $tableName = DB_INVOICE.'.INVOICE_HISTORY';

    /**
     * Primary key of invoice history
     *
     * @var string
     */
    protected $primaryKey = 'INVOICE_ID';

    /**
     * Get all History value
     *
     * @return array
     */
    public function all()
    {
        return $this->select("SELECT * from $this->tableName order by {$this->primaryKey} desc")->fetchAll();
    }

    /**
     * Delete invoice with product
     *
     * @return bool
     */
    public function deleteWithProduct()
    {
        try {
            $this->beginTransaction();
            $productModel = $this->product();
            $this->deleteInvoiceFile();
            $productModel->deleteByOwner($productModel::HISTORY_PRODUCT, $this->key());
            $deleted = $this->delete();
            $this->commit();

            return $deleted;
        } catch (\Exception $e) {
            $this->rollBack();
            \Logger::getLogger("service")->error($e->getMessage());
            \Logger::getLogger("service")->error($e->getTraceAsString());
            throw new Exception("Failed Delete Invoice");
        }
    }

    /**
     * Get invoice history base on profile invoice
     *
     * @param Int $profileId
     * @return  array
     */
    public function whereProfile($profileId)
    {
        $query = "SELECT * FROM {$this->tableName}
            WHERE PROFILE_ID = {$profileId}
            ORDER BY STATUS ASC, INVOICE_NUMBER DESC";

        return $this->select($query)->fetchAll();
    }

    /**
     * Get invoice history base on period
     *
     * @param int $timestamp
     * @return  array
     */
    public function whereStartDate($timestamp)
    {
        if (!strtotime("@$timestamp")) {
            return;
        }

        $date = date('m-Y', $timestamp);

        $query = "SELECT * FROM {$this->tableName}
            WHERE DATE_FORMAT(START_DATE, '%m-%Y') = '{$date}'
            ORDER BY START_DATE DESC";
        return $this->select($query)->fetchAll();
    }

    /**
     * Get all History with product value
     *
     * @param string $historyId
     * @return array
     */
    public function withProduct($historyId = null)
    {
        if (is_null($historyId)) {
            $data = $this->all();
        } else {
            if (!$model = $this->find($historyId)) {
                throw new Exception("History Not Found");
            }
            $data = [$model];
        }

        $this->loadProduct($data);

        return $data;
    }

    /**
     * Load product for historys
     *
     * @param array $data
     * @return array
     */
    public function loadProduct(array &$data = null)
    {
        if (is_null($data)) {
            return $this->products = $this->getProduct($this->key());
        }

        $historyIds = array_column($data, $this->keyName());
        $products = $this->getProduct($historyIds);
        $products = $this->groupBy($products, 'ownerId');

        foreach ($data as &$item) {
            $item['products'] = $products[$item->key()] ?? null;
        }
    }

    /**
     * Get product that belongsto history
     *
     * @param int|array $historyId
     * @return array
     */
    public function getProduct($historyId)
    {
        if (empty($historyId)) {
            return [];
        }

        return $this->product()->history($historyId);
    }

    /**
     * Get product instance
     *
     * @return InvoiceProduct
     */
    public function product()
    {
        return new InvoiceProduct();
    }

    /**
     * Get product instance
     *
     * @return InvoiceProduct
     */
    public function profile()
    {
        return new InvoiceProfile();
    }

    /**
     * Perform update History
     *
     * @param string $key
     * @param  array $data
     * @return void
     */
    public function updateHistory($key, array $data)
    {
        if (!$model = $this->find($key)) {
            throw new Exception("History Not Found");
        }

        $data['updateAt'] = date('Y-m-d H:i:s');

        return $model->update($data);
    }

    /**
     * Create History
     *
     * @param array $data
     * @return  int
     */
    public function createHistory(array $data)
    {
        $this->beginTransaction();
        $invoiceId = $this->insert($data);
        $this->insertProductFromProfile($data, $invoiceId);
        $this->commit();

        return $invoiceId;
    }

    /**
     * Insert invoice product for current history base on
     * invoice profile
     *
     * @param array $data
     * @param Int $invoiceId
     * @return void
     */
    protected function insertProductFromProfile($data, $invoiceId)
    {
        $profile = $this->profile()->withProduct($data['profileId']);

        if (empty($profile)) {
            throw new Exception("Profile not found");
        }

        if ($products = $profile[0]->products) {
            foreach ($products as $product) {
                $product->profile2History($data, $invoiceId);
            }
        }
    }

    /**
     * validate invoice number is duplicate or not
     *
     * @param String $invoiceNumber
     * @param mixed $invoiceId
     * @return  bool
     */
    public function isInvoiceNumberDuplicate($invoiceNumber, $invoiceId = null)
    {
        $query = "SELECT count(1) from {$this->tableName} where INVOICE_NUMBER = '{$invoiceNumber}'";

        if ($invoiceId) {
            $query .= " AND {$this->primaryKey} != $invoiceId";
        }

        return $this->select($query)->fetchColumn() > 0;
    }

    /**
     * Get sub total product
     *
     * @return float
     */
    public function subTotal()
    {
        if (empty($this->products)) {
            return 0;
        }

        return array_reduce($this->products, function ($carry, $product) {
            $carry += $product->amount();
            return round($carry, 2);
        });
    }

    /**
     * Get total products price that already subdivide with VAT
     *
     * @return float
     */
    public function total()
    {
        return round($this->subTotal() + $this->vat(), 2);
    }

    /**
     * Get total products price that already subdivide with VAT
     *
     * @return float
     */
    public function spellTotal()
    {
        $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        $totalWord = $fmt->format($this->total());

        return ucwords($totalWord);
    }

    /**
     * Get VAT value
     *
     * @return float
     */
    public function vat()
    {
        return $this->subTotal() * 0.1;
    }

    /**
     * Get term of payment value
     *
     * @return integer
     */
    public function paymentPeriod()
    {
        $dueDate = new DateTime($this->dueDate);
        $startDate = new DateTime($this->startDate);

        return $dueDate->diff($startDate)->format("%d");
    }

    /**
     * Determine whether Invoices already exist this month
     *
     * @param string $paymentDate
     * @param integer $profileId
     * @param integer $invoiceId
     * @return boolean
     */
    public function isInvoiceAlreadyExists($paymentDate, $profileId, $invoiceId = null)
    {
        $date = new DateTime($paymentDate);
        $monthYear = $date->format('Y-m');
        $query = "SELECT COUNT(*) FROM {$this->tableName}
            WHERE DATE_FORMAT(START_DATE, \"%Y-%m\") = \"{$monthYear}\"
            AND PROFILE_ID = $profileId";

        if (!empty($invoiceId)) {
            $query .= " AND INVOICE_ID != $invoiceId ";
        }

        return $this->select($query)->fetchColumn() > 0;
    }

    /**
     * Invoice file name
     *
     * @return  String
     */
    public function generateFileName($profile, $setting)
    {
        $date = new DateTime($this->startDate);
        $date = $date->format('F-Y');
        $fileName = $profile->customerId . '_' . $setting->invoiceNumberPrefix . $this->invoiceNumber . '_' . $profile->companyName . '_' . $date;

        return preg_replace('/\s+/', '_', $fileName) . ".pdf";
    }

    /**
     * Get invoice file path
     *
     * @return string
     */
    public function filePath()
    {
        return SMSAPIADMIN_INVOICE_DIR . $this->fileName;
    }

    /**
     * Create invoice folder
     *
     * @return string
     */
    public function createFolder()
    {
        if (!file_exists(SMSAPIADMIN_INVOICE_DIR)) {
            mkdir(SMSAPIADMIN_INVOICE_DIR, 0777, true);
        }
    }

    /**
     * Determain invoice file exists
     *
     * @return  bool
     */
    public function fileExists()
    {
        return !empty($this->fileName) && file_exists($this->filePath());
    }

    /**
     * Delete invoice file
     *
     * @return void
     */
    public function deleteInvoiceFile()
    {
        if ($this->fileExists()) {
            unlink($this->filePath());
        }
    }

    /**
     * Get invoice profile for current invoice
     *
     * @return  InvoiceProfile|null
     */
    public function getProfile()
    {
        return $this->profile()->find($this->profileId);
    }

    /**
     * Get invoice setting
     *
     * @return  InvoiceSetting|null
     */
    public function getSetting()
    {
        return (new InvoiceSetting)->getSetting();
    }

    /**
     * Create Invoice File
     *
     * @return void
     */
    public function createInvoiceFile()
    {
        $this->loadProduct();
        $profile = $this->getProfile();
        $setting = $this->getSetting();
        $page = \SmsApiAdmin::getTemplate();

        $page->assign('profile', $profile);
        $page->assign('setting', $setting);
        $page->assign('invoice', $this);
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

        $this->createFolder();
        $fileName = $this->generateFileName($profile, $setting);

        $mpdf->Output(SMSAPIADMIN_INVOICE_DIR . $fileName, Destination::FILE);
        return $this->update(compact('fileName'));
    }

    /**
     * Download Invoice File
     *
     * @return void
     */
    public function downloadFile()
    {

        if ($this->fileExists()) {
            $filePath = $this->filePath();
            ob_start();
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: public');
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($filePath));
            ob_end_flush();
            @readfile($filePath);
        } else {
            http_response_code(404);
            echo "File Not Found ";
        }
    }

    /**
     * Preview Invoice File
     *
     * @return void
     */
    public function previewFile()
    {
        if ($this->fileExists()) {
            $filePath = $this->filePath();
            header('Content-Type: application/pdf');
            header('Content-disposition: inline; filename="' . basename($filePath) . '"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            @readfile($filePath);
        } else {
            http_response_code(404);
            echo "File Not Found";
        }
    }

    /**
     * Check invoice is paid
     *
     * @return bool
     */
    public function isLock()
    {
        return self::INVOICE_LOCK === intval($this->status);
    }
}
