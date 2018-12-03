<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\lib\model\InvoiceProfile;
use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use Firstwap\SmsApiAdmin\lib\Modules\InvoiceGenerator;
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
    const INVOICE_LOCK   = 1;

    /**
     * Constance Invoice Type
     * ORIGINAL, COPY, REVISED
     */
    const ORIGINAL = "ORIGINAL";
    const COPIED   = "COPIED";
    const REVISED  = "REVISED";

    /**
     * Minimum Commitment Constant
     */
    const MINIMUM_QTY   = "QUANTITY";
    const MINIMUM_PRICE = "PRICE";

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
        return $this->select("SELECT * from $this->tableName ORDER BY STATUS ASC, INVOICE_NUMBER DESC, {$this->tableName}.INVOICE_ID DESC")->fetchAll();
    }

    /**
     * Get all Unlocked invoice
     *
     * @param  String  $status
     * @return array
     */
    public function whereStatus($status = null)
    {
        $query = "SELECT * FROM {$this->tableName} "
            . " LEFT JOIN " . DB_INVOICE . ".INVOICE_PROFILE ON {$this->tableName}.PROFILE_ID = INVOICE_PROFILE.PROFILE_ID "
            . " LEFT JOIN " . DB_SMS_API_V2 . ".CLIENT on " . DB_SMS_API_V2 . ".CLIENT.CLIENT_ID = INVOICE_PROFILE.CLIENT_ID "
            . " WHERE ARCHIVED_DATE is null";

        if (strtolower($status) === 'locked' || $status === self::INVOICE_LOCK)
        {
            $query .= " AND STATUS = " . self::INVOICE_LOCK;
        }
        elseif (strtolower($status) === 'unlocked' || $status === self::INVOICE_UNLOCK)
        {
            $query .= " AND STATUS = " . self::INVOICE_UNLOCK;
        }

        $query .= " ORDER BY STATUS ASC, INVOICE_NUMBER DESC, {$this->tableName}.INVOICE_ID DESC";

        return $this
            ->select($query)
            ->fetchAll();
    }

    /**
     * Get pending count invoice history
     *
     * @return int
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
        }
        catch (\Exception $e)
        {
            $this->rollBack();
            \Logger::getLogger("service")->error($e->getMessage());
            \Logger::getLogger("service")->error($e->getTraceAsString());
            throw new Exception("Failed Delete Invoice");
        }
    }

    /**
     * Get invoice history base on profile invoice
     *
     * @param  Int     $profileId
     * @return array
     */
    public function whereProfile($profileId)
    {
        $query = "SELECT * FROM {$this->tableName}
            WHERE PROFILE_ID = {$profileId}
            ORDER BY STATUS ASC, INVOICE_NUMBER DESC, {$this->tableName}.INVOICE_ID DESC";

        return $this->select($query)->fetchAll();
    }

    /**
     * Get invoice history base on period
     *
     * @param  int     $timestamp
     * @return array
     */
    public function whereStartDate($timestamp)
    {
        if (!strtotime("@$timestamp"))
        {
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
     * @param  string  $historyId
     * @return array
     */
    public function withProduct($historyId = null)
    {
        if (is_null($historyId))
        {
            $data = $this->all();
        }
        else
        {
            if (!$model = $this->find($historyId))
            {
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
     * @param  array   $data
     * @return array
     */
    public function loadProduct(array &$data = null)
    {
        if (is_null($data))
        {
            return $this->products = $this->getProduct($this->key());
        }

        $historyIds = array_column($data, $this->keyName());
        $products   = $this->getProduct($historyIds);
        $products   = $this->groupBy($products, 'ownerId');

        foreach ($data as &$item)
        {
            $item['products'] = $products[$item->key()] ?? null;
        }
    }

    /**
     * Get product that belongsto history
     *
     * @param  int|array $historyId
     * @return array
     */
    public function getProduct($historyId)
    {
        if (empty($historyId))
        {
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
     * Create History ['profileId', 'invoiceNumber', 'startDate', 'dueDate', 'refNumber', 'invoiceType']
     *
     * @param  array $data Format $data should have attributes :
     * @return int
     */
    public function createHistory(array $data)
    {
        $this->beginTransaction();
        $invoiceId = $this->insert($data);
        $this->insertProductFromProfile($data, $invoiceId);
        $this->commit();

        $invoice = $this->find($invoiceId);
        $invoice->createInvoiceFile();

        return $invoiceId;
    }

    /**
     * Duplicate invoice from existing one
     *
     * @param  array $data An Array of updated attribute values
     * @return int
     */
    public function duplicateInvoice(array $data = [])
    {
        $attributes = array_merge($this->attributes, $data);
        $products   = $this->loadProduct();

        $attributes['fileName'] = null;
        unset($attributes[$this->keyName()]);
        unset($attributes['createdAt']);
        unset($attributes['products']);

        $this->beginTransaction();

        $invoiceId = $this->insert($attributes);

        if (!empty($products))
        {
            foreach ($products as &$product)
            {
                $product->setKey(null);
                $product->save(['ownerId' => $invoiceId]);
            }
        }

        $this->setKey($invoiceId);

        $this->commit();

        $this->createInvoiceFile();

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

        $this->update([
            'status'   => self::INVOICE_LOCK,
            'lockedAt' => date('Y-m-d H:i:s'),
        ]);

        $this->createInvoiceFile();
    }

    /**
     * Create new copied invoice from existing invoice
     *
     * @return int
     */
    public function copyInvoice()
    {
        $lastUsage  = $this->lastInvoiceUsage(self::COPIED);
        $attributes = $this->attributes();

        $attributes['invoiceUsage'] = $lastUsage + 1;
        $attributes['invoiceType']  = self::COPIED;
        $attributes['lockedAt']     = date('Y-m-d H:i:s');

        return $this->duplicateInvoice($attributes);
    }

    /**
     * Change invoice status from unlocked to locked
     *
     * @return int
     */
    public function reviseInvoice()
    {
        if ($existingRevised = $this->hasExistingInvoiceRevise())
        {
            $this->attributes = $existingRevised->attributes();
            return $this->key();
        }

        $lastUsage                  = $this->lastInvoiceUsage(self::REVISED);
        $attributes                 = $this->attributes();
        $attributes['lockedAt']     = null;
        $attributes['invoiceUsage'] = $lastUsage + 1;
        $attributes['invoiceType']  = self::REVISED;
        $attributes['status']       = self::INVOICE_UNLOCK;

        return $this->duplicateInvoice($attributes);
    }

    /**
     * Check invoice that has unlocked status for revised invoice
     * User can not revise invoice when there is another invoice
     * with same invoice number has revise type and the status is unlocked
     *
     * @return self|null
     */
    public function hasExistingInvoiceRevise()
    {
        $unlockedStatus = self::INVOICE_UNLOCK;
        $revisedType    = self::REVISED;

        $query = "SELECT * FROM {$this->tableName}
            WHERE
                INVOICE_NUMBER   = {$this->invoiceNumber}
                AND PROFILE_ID   = {$this->profileId}
                AND STATUS       = {$unlockedStatus}
                AND INVOICE_TYPE = '{$revisedType}'
        ";

        return $this->select($query)->fetch();
    }

    /**
     * Get last Invoice usage value
     *
     * @param  string $invoiceType
     * @return int
     */
    public function lastInvoiceUsage($invoiceType = null)
    {
        $query = "SELECT COUNT(1) as TOTAL FROM {$this->tableName} WHERE
            INVOICE_NUMBER  = {$this->invoiceNumber} AND
            PROFILE_ID  = {$this->profileId}
        ";

        if (!empty($invoiceType))
        {
            $query .= " AND INVOICE_TYPE = '" . strtoupper($invoiceType) . "'";
        }

        return (int) $this->select($query)->fetchColumn();
    }

    /**
     * Insert invoice product for current history base on
     * invoice profile
     *
     * @param  array  $data
     * @param  Int    $invoiceId
     * @return void
     */
    protected function insertProductFromProfile($data, $invoiceId)
    {
        $profile = $this->profile()->withProduct($data['profileId']);

        if (empty($profile))
        {
            throw new Exception("Profile not found");
        }

        if ($products = $profile[0]->products)
        {
            foreach ($products as $product)
            {
                $product->profile2History($data, $invoiceId);
            }
        }
    }

    /**
     * validate invoice number is duplicate or not
     *
     * @param  String $invoiceNumber
     * @param  mixed  $invoiceId
     * @return bool
     */
    public function isInvoiceNumberDuplicate($invoiceNumber, $invoiceId = null)
    {
        $query = " SELECT count(1) from {$this->tableName} where INVOICE_NUMBER = '{$invoiceNumber}' ";

        if ($invoiceId)
        {
            $query .= " AND {$this->primaryKey} != $invoiceId";
        }

        return $this->select($query)->fetchColumn() > 0;
    }

    /**
     * Get sub total product
     *
     * @param  boolan  $useReport
     * @return float
     */
    public function subTotal($useReport = false)
    {
        if (empty($this->products))
        {
            return 0;
        }

        if ($useReport)
        {
            return array_reduce($this->products, function ($carry, $product)
            {
                if ((int) $product->useReport !== 0)
                {
                    $carry += $product->amount();
                }
                return round($carry, 2);
            }, 0);
        }

        return array_reduce($this->products, function ($carry, $product)
        {
            $carry += $product->amount();
            return round($carry, 2);
        }, 0);
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
        $fmt       = new NumberFormatter('en', NumberFormatter::SPELLOUT);
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
        $dueDate   = new DateTime($this->dueDate);
        $startDate = new DateTime($this->startDate);

        return $dueDate->diff($startDate)->format("%d");
    }

    /**
     * Determine whether Invoices already exist this month
     *
     * @param  string    $paymentDate
     * @param  integer   $profileId
     * @param  integer   $invoiceId
     * @return boolean
     */
    public function isInvoiceAlreadyExists($paymentDate, $profileId, $invoiceId = null)
    {
        $date      = new DateTime($paymentDate);
        $monthYear = $date->format('Y-m');
        $query     = "SELECT COUNT(*) FROM {$this->tableName}
            WHERE DATE_FORMAT(START_DATE, \"%Y-%m\") = \"{$monthYear}\"
            AND PROFILE_ID = $profileId";

        if (!empty($invoiceId))
        {
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
     * @return bool
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
        if ($this->fileExists())
        {
            if (unlink($this->filePath()))
            {
                \Logger::getLogger("service")->info("Success Delete File: " . $this->filePath());
                return true;
            }
            else
            {
                \Logger::getLogger("service")->error("Failed Delete File: " . $this->filePath());
                return false;
            }
        }
        else
        {
            \Logger::getLogger("service")->warn("File Not Found: " . $this->filePath());
            return false;
        }
    }

    /**
     * Get invoice profile for current invoice
     *
     * @return InvoiceProfile|null
     */
    public function getProfile()
    {
        return $this->profile()->find($this->profileId);
    }

    /**
     * Get invoice setting
     *
     * @return InvoiceSetting
     */
    public function getSetting()
    {
        return (new InvoiceSetting())->getSetting();
    }

    /**
     * Create Invoice File
     *
     * @return void
     */
    public function createInvoiceFile()
    {
        $this->deleteInvoiceFile();

        $profile  = $this->getProfile();
        $setting  = $this->getSetting();
        $fileName = $this->generator()->createPdfFile($this, $profile, $setting);

        return $this->update(compact('fileName'));
    }

    /**
     * Generator Invoice instance
     *
     * @return InvoiceGenerator
     */
    protected function generator()
    {
        return new InvoiceGenerator();
    }

    /**
     * Download Invoice File
     *
     * @return void
     */
    public function downloadFile()
    {
        if ($this->fileExists())
        {
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
        }
        else
        {
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
        if ($this->fileExists())
        {
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
        }
        else
        {
            http_response_code(404);
            echo "File Not Found";
        }
    }

    /**
     * Check invoice is locked or not
     *
     * @return bool
     */
    public function isLock()
    {
        return self::INVOICE_LOCK === intval($this->status);
    }

    /**
     * Check invoice is already expired
     *
     * @return bool
     */
    public function isExpired()
    {
        $dueDate     = strtotime($this->dueDate ?? 'today midnight');
        $currentDate = strtotime('today midnight');

        return $dueDate < $currentDate;
    }

    /**
     * get total quantity of current product(product with useReport only)
     *
     * @return int
     */
    public function totalQty()
    {
        if (empty($this->products))
        {
            return 0;
        }

        return array_reduce($this->products, function ($carry, $product)
        {
            if ((int) $product->useReport !== 0)
            {
                $carry += $product->qty;
            }
            return $carry;
        }, 0);

    }

    /**
     * Create new object for minimum commitment product
     *
     * @param  String           $productName
     * @param  float            $productPrice
     * @return InvoiceProduct
     */
    public function makeNewProduct($productName, $productPrice)
    {
        $object              = new InvoiceProduct();
        $object->productName = $productName;
        $object->unitPrice   = $productPrice;
        $object->qty         = 1;

        return $object;
    }

    /**
     * Create minimum commitment for each product
     *
     * @param  String  $minimumType
     * @param  String  $productName
     * @param  Float   $commitmentValue
     * @param  Float   $minCharge
     * @return array
     */
    public function notCombined($minimumType, $productName, $commitmentValue, $minCharge)
    {
        $appendedProduct = [];

        foreach ($this->products as $product)
        {
            $value = ($minimumType == static::MINIMUM_PRICE) ? $product->amount() : $product->qty;
            if (((int) $value < (int) $commitmentValue) && ((int) $product->useReport !== 0))
            {
                $newCommitmentPrice = ($minimumType == static::MINIMUM_PRICE) ? $commitmentValue - $product->amount() : $minCharge;
                $newName            = $productName . $product->productName;
                array_push($appendedProduct, $this->makeNewProduct($newName, $newCommitmentPrice));
            }
        }

        return $appendedProduct;
    }

    /**
     * To Calculate minimum commitment based on invoice profile
     *
     * @param  InvoiceProfile $profile
     * @return array
     */
    public function minimumCommitment($profile)
    {
        $products = $this->products;

        if (!empty($products))
        {
            if ($profile['minCommitmentType'] === static::MINIMUM_PRICE)
            {
                if ($profile['combinedMinCommitment'])
                {
                    $subTotal = $this->subTotal(true);

                    if ($subTotal < (int) $profile['minCommitmentAmount'])
                    {
                        $products[] = $this->makeNewProduct('Minimum Commitment Surcharge (Price Combined)', $profile['minCommitmentAmount'] - $subTotal);
                    }
                }
                else
                {
                    $newProducts = $this->notCombined($profile['minCommitmentType'], 'Minimum Commitment Surcharge (Price) for ', $profile['minCommitmentAmount']);
                    $products    = array_merge($products, $newProducts);
                }
            }
            elseif ($profile['minCommitmentType'] === static::MINIMUM_QTY)
            {
                if ($profile['combinedMinCommitment'])
                {
                    if ($this->totalQty() < (int) $profile['minCommitmentAmount'] && (int) $this->totalQty() > 0)
                    {
                        $products[] = $this->makeNewProduct('Minimum Commitment Surcharge (Quantity Combined) ', $profile['minCharge']);
                    }
                }
                else
                {
                    $newProducts = $this->notCombined($profile['minCommitmentType'], 'Minimum Commitment Surcharge (Quantity) for ', $profile['minCommitmentAmount'], $profile['minCharge']);
                    $products    = array_merge($products, $newProducts);
                }
            }
            $this->products = $products;
        }
        return $products;
    }

}
