<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\Modules\InvoiceGenerator;
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
     * Constance Invoice Type
     * ORIGINAL, COPY, REVISED
     */
    const ORIGINAL  = "ORIGINAL";
    const COPY      = "COPY";
    const REVISED   = "REVISED";

    /**
     * Table name of invoice history
     *
     * @var string
     */
    protected $tableName = DB_INVOICE . '.INVOICE_HISTORY';

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
        return $this->select("SELECT * from $this->tableName ORDER BY STATUS ASC, INVOICE_NUMBER DESC")->fetchAll();
    }

    /**
     * Get all Unlocked invoice
     *
     * @param String $status
     * @return array
     */
    public function whereStatus($status = null)
    {
        $query = "SELECT * FROM {$this->tableName} "
            . " LEFT JOIN " . DB_INVOICE . ".INVOICE_PROFILE ON {$this->tableName}.PROFILE_ID = INVOICE_PROFILE.PROFILE_ID "
            . " LEFT JOIN ".DB_SMS_API_V2.".CLIENT on ".DB_SMS_API_V2.".CLIENT.CLIENT_ID = INVOICE_PROFILE.CLIENT_ID ";

        if (strtolower($status) === 'locked' || $status === self::INVOICE_LOCK)
        {
            $query .= " WHERE STATUS = " . self::INVOICE_LOCK;
        }
        else if (strtolower($status) === 'unlocked' || $status === self::INVOICE_UNLOCK)
        {
            $query .= " WHERE STATUS = " . self::INVOICE_UNLOCK;
        }

        $query .= " ORDER BY STATUS ASC, INVOICE_NUMBER DESC";

        return $this
            ->select($query)
            ->fetchAll();
    }

    /**
     * Get pending count invoice history
     *
     * @return  int
     */
    public function pendingCount()
    {
        $query = "SELECT count(*) AS total FROM {$this->tableName} WHERE STATUS = " . self::INVOICE_UNLOCK . " LIMIT 1";

        return $this
            ->select($query)
            ->fetchColumn();
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

        $data['updatedAt'] = date('Y-m-d H:i:s');

        return $model->update($data);
    }

    /**
     * Create History
     *
     * @param array $data   Format $data should have attributes :
     *                      ['profileId', 'invoiceNumber', 'startDate', 'dueDate', 'refNumber', 'invoiceType']
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
     * Change invoice status from unlocked to locked
     *
     * @return void
     */
    public function lockInvoice()
    {
        $this->deleteInvoiceFile();
        $this->update(['status' => InvoiceHistory::INVOICE_LOCK]);
        $this->createInvoiceFile();
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
     * Get invoice file path
     *
     * @return string
     */
    public function filePath()
    {
        return SMSAPIADMIN_INVOICE_DIR . $this->fileName;
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
            if (unlink($this->filePath())) {
                \Logger::getLogger("service")->info("Success Delete File: ".$this->filePath());
                return true;
            } else {
                \Logger::getLogger("service")->error("Failed Delete File: ".$this->filePath());
                return false;
            }
        } else {
            \Logger::getLogger("service")->warn("File Not Found: ".$this->filePath());
            return false;
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
     * @return  InvoiceSetting
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
        $profile    = $this->getProfile();
        $setting    = $this->getSetting();
        $fileName   = $this->generator()->createPdfFile($this, $profile, $setting);

        return $this->update(compact('fileName'));
    }

    /**
     * Generator Invoice instance
     *
     * @return InvoiceGenerator
     */
    protected function generator()
    {
        return new InvoiceGenerator;
    }

    /**
     * Download Invoice File
     *
     * @return void
     */
    public function downloadFile()
    {

        if ($this->fileExists()) {
            ob_start();
            $filePath = $this->filePath();
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: public');
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($filePath));
            ob_end_clean();
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
            ob_start();
            $filePath = $this->filePath();
            header('Content-Type: application/pdf');
            header('Content-disposition: inline; filename="' . basename($filePath) . '"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            ob_end_clean();
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
