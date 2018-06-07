<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use \Exception;

/**
 * Model for INVOICE_PRODUCT table
 * This model use to get and update Product for invoice
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InvoiceProduct extends ModelContract
{
    /**
     * Product Owner Type Constant
     */
    const PROFILE_PRODUCT = "PROFILE";
    const HISTORY_PRODUCT = "HISTORY";

    /**
     * Table name of invoice profile
     *
     * @var string
     */
    protected $tableName = DB_INVOICE . '.INVOICE_PRODUCT';

    /**
     * Primary key of invoice profile
     *
     * @var string
     */
    protected $primaryKey = 'PRODUCT_ID';

    /**
     * Get all Product value
     *
     * @return array
     */
    public function all()
    {
        $query = "SELECT * from $this->tableName order by {$this->primaryKey} desc";
        return $this->select($query)->fetchAll();
    }

    /**
     * Get Product that belongs to profile
     *
     * @param string|array $profileId
     * @return array|null
     */
    public function profile($profileId = null)
    {
        return $this->productType(self::PROFILE_PRODUCT, $profileId);
    }

    /**
     * Get Product that belongs to history
     *
     * @param string|array $historyId
     * @return array|null
     */
    public function history($historyId = null)
    {
        return $this->productType(self::HISTORY_PRODUCT, $historyId);
    }

    /**
     * Insert new product
     *
     * @param array $data
     * @return int
     */
    public function insertProduct(array $data)
    {
        $this->attributes = $data;

        if ($this->isHistory() && $this->useReport === 1) {
            $this->setQtyAndUnitPriceFromReport();
        }

        return $this->save();
    }

    /**
     * Delete product base on owner
     *
     * @param String $ownerType
     * @param Int $ownerId
     * @return  bool
     */
    public function deleteByOwner($ownerType, $ownerId)
    {
        $query = "DELETE FROM {$this->tableName}
            WHERE OWNER_TYPE = \"$ownerType\" AND OWNER_ID={$ownerId} ";

        $stmt = $this->db->prepare($query);

        return $stmt->execute();
    }

    /**
     * Get product that belongs to owner type
     *
     * @param string $ownerType
     * @param string|array $ownerId
     * @return array
     */
    protected function productType($ownerType = null, $ownerId = null)
    {
        $ownerType = strtoupper($ownerType) === self::PROFILE_PRODUCT
        ? self::PROFILE_PRODUCT
        : self::HISTORY_PRODUCT;

        $query = "SELECT * from $this->tableName WHERE OWNER_TYPE = \"$ownerType\" ";

        if ($ownerId !== null) {
            if (is_array($ownerId)) {
                $ownerId = implode(', ', array_map(function ($item) {
                    return "'$item'";
                }, $ownerId));
                $query .= " AND OWNER_ID IN ($ownerId) ";
            } else {
                $query .= " AND OWNER_ID = \"$ownerId\" ";
            }
        }

        return $this->select($query)->fetchAll();
    }

    /**
     * Transfer to history product
     *
     * @param array $data
     * @param int $ownerId
     * @return bool
     */
    public function profile2History(array $data, $ownerId)
    {
        $this->period = $this->getPeriodDate($data['startDate']);
        $this->ownerType = self::HISTORY_PRODUCT;
        $this->ownerId = $ownerId;

        if ($this->useReport) {
            $this->setQtyAndUnitPriceFromReport();
        }

        $data = $this->attributes();
        unset($data[$this->keyName()]);

        return $this->insert($data);
    }

    /**
     * Get period date value from invoice date
     * period date is first day of previous month
     *
     * @param string $invoiceDate
     * @return string
     */
    protected function getPeriodDate($invoiceDate)
    {
        $period = strtotime("$invoiceDate -1month last day of this month");

        return date('Y-m-d', $period);
    }

    /**
     * Set quantity and unit price base on summary report
     *
     * @return  void
     */
    protected function setQtyAndUnitPriceFromReport()
    {
        $period = strtotime($this->period);

        if ($period === false || empty($this->reportName)) {
            return;
        }

        $userApi = null;
        $qty = $unitPrice = 0;
        $reportPath = $this->summaryPath($period, $this->reportName);

        if (file_exists($reportPath)) {
            list($qty, $unitPrice, $userApi) = $this->getSummaryValue($reportPath);
        }

        $this->qty = $qty;
        $this->unitPrice = $unitPrice;
        $this->userApiReport = $userApi;
    }

    /**
     * Get summary path
     *
     * @param int $period         Timestamps value from period
     * @param String $reportName  Name of Summary report
     * @return  String
     */
    protected function summaryPath($period, $reportName)
    {
        $month = date('m', $period);
        $year = date('Y', $period);
        $reportDir = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . $year . '/' . $month . '/FINAL_STATUS/';

        return $reportDir . $reportName . '_' . date('M_Y', $period) . '_Summary.xlsx';
    }

    /**
     * Read excel file
     *
     * @param String $filePath
     * @return  array
     */
    protected function getSummaryValue($filePath)
    {
        $qty = $unitPrice = 0;
        $userApi = null;

        try {
            $reader = $this->getExcelReader();
            $sheet = $reader->load($filePath)->getActiveSheet();
            $sms = $sheet->getCell(SUMMARY_TOTAL_SMS_CHARGED_CELL)->getValue();
            $price = $sheet->getCell(SUMMARY_TOTAL_PRICE_CELL)->getValue();
            $userApi = $sheet->getCell(SUMMARY_USER_API_CELL)->getValue();

            if ($qty = intval($sms)) {
                $unitPrice = round(floatval($price) / $qty);
            }
        } catch (\Exception $e) {

            \Logger::getLogger("service")->error($e->getTraceAsString());
        }

        return [$qty, $unitPrice, $userApi];
    }

    /**
     * Get excel Reader
     *
     * @return  Excel2007
     */
    protected function getExcelReader()
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);

        return $reader;
    }

    /**
     * Perform update Product
     *
     * @param string $key
     * @param  array $data
     * @return void
     */
    public function updateProduct($key, array $data)
    {
        if (!$model = $this->find($key)) {
            throw new Exception("Product Not Found");
        }

        $data['updateAt'] = date('Y-m-d H:i:s');

        $newData = array_intersect_key($data, $model->attributes());

        $this->attributes = array_merge($this->attributes, $model->attributes(), $newData);

        if ($this->isHistory() && intval($this->useReport) === 1) {
            $this->setQtyAndUnitPriceFromReport();
        }

        $this->update($this->attributes);
    }

    /**
     * Get amount product
     *
     * @return  float
     */
    public function amount()
    {
        $qty = intval($this->qty);
        $unitPrice = floatval($this->unitPrice);

        return $qty * $unitPrice;
    }

    /**
     * Get last period
     *
     * @return string
     */
    public function lastPeriod()
    {
        return strtotime($this->period . " last day of this month");
    }

    /**
     * Determine if current product is "HISTORY".
     *
     * @return bool
     */
    public function isHistory()
    {
        return $this->ownerType === self::HISTORY_PRODUCT;
    }

    /**
     * Determine if current product is "PROFILE".
     *
     * @return bool
     */
    public function isProfile()
    {
        return $this->ownerType === self::PROFILE_PRODUCT;
    }
}
