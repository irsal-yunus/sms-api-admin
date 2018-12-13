<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use \Exception;

require_once SMSAPIADMIN_LIB_DIR . 'model/ApiReport.php';

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

    const SUMMARY_INTERNATIONAL_PRICE_CELL = 'B4';
    const SUMMARY_USER_API_CELL            = 'B5';
    const SUMMARY_TOTAL_SMS_CHARGED_CELL   = 'B11';
    const SUMMARY_TOTAL_PRICE_CELL         = 'B12';

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
     * @return int|array
     */
    public function insertProduct(array $data)
    {
        $this->attributes = $data;

        if ($this->isHistory() && intval($this->useReport) === 1)
        {
            return $this->setQtyAndUnitPriceFromReport();
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

        if ($ownerId !== null)
        {
            if (is_array($ownerId))
            {
                $ownerId = implode(', ', array_map(function ($item)
                {
                    return "'$item'";
                }, $ownerId));
                $query .= " AND OWNER_ID IN ($ownerId) ";
            }
            else
            {
                $query .= " AND OWNER_ID = \"$ownerId\" ";
            }
        }

        return $this->select($query)->fetchAll();
    }

    /**
     * Transfer to history product
     *
     * @param array $data
     * @param int $historyId
     * @return int|array
     */
    public function profile2History($data, $historyId)
    {
        $this->period    = $this->getPeriodDate($data['startDate']);
        $this->ownerType = self::HISTORY_PRODUCT;
        $this->ownerId   = $historyId;
        $this->setKey(null);

        if ($this->useReport)
        {
            return $this->setQtyAndUnitPriceFromReport();
        }

        $data = $this->attributes();

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
     * @return  int|array
     */
    protected function setQtyAndUnitPriceFromReport()
    {
        $userApi = null;
        $qty     = $unitPrice     = 0;
        $period  = strtotime($this->period);

        if ($period !== false && !empty($this->reportName))
        {
            $reportPath = $this->summaryPath($period, $this->reportName);

            if (file_exists($reportPath))
            {
                $results = $this->getSummaryValue($reportPath);
                if (is_array(current($results)))
                {
                    return $this->storeIntlProducts($results);
                }
                elseif (isset($results['qty']))
                {
                    $qty       = $results['qty'];
                    $unitPrice = $results['unitPrice'];
                    $userApi   = $results['userApi'];
                }
            }
        }

        $this->qty           = $qty;
        $this->unitPrice     = $unitPrice;
        $this->userApiReport = $userApi;

        return $this->save();
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
        $month     = date('m', $period);
        $year      = date('Y', $period);
        $reportDir = SMSAPIADMIN_ARCHIEVE_EXCEL_REPORT . $year . '/' . $month . '/FINAL_STATUS/';

        return $reportDir . $reportName . '_' . date('M_Y', $period) . '_Summary.xlsx';
    }

    /**
     * Get summary values by read summary report file
     *
     * @param String $filePath  Summary Report file path
     * @return  array
     */
    protected function getSummaryValue($filePath)
    {
        try {
            $reader    = $this->getExcelReader();
            $sheet     = $reader->load($filePath)->getActiveSheet();
            $intlPrice = $sheet->getCell(static::SUMMARY_INTERNATIONAL_PRICE_CELL)->getValue();

            if (strtoupper($intlPrice) === 'YES')
            {
                return $this->getIntlSummaryValues($sheet);
            }

            $qty     = $unitPrice     = 0;
            $userApi = null;
            $sms     = $sheet->getCell(static::SUMMARY_TOTAL_SMS_CHARGED_CELL)->getValue();
            $price   = $sheet->getCell(static::SUMMARY_TOTAL_PRICE_CELL)->getValue();
            $userApi = $sheet->getCell(static::SUMMARY_USER_API_CELL)->getValue();

            if (($qty = $this->toFloat($sms)) > 0)
            {
                $unitPrice = round($this->toFloat($price) / $qty, 2);
            }

            return [
                'qty'       => $qty,
                'unitPrice' => $unitPrice,
                'userApi'   => $userApi,
            ];
        }
        catch (\Exception $e)
        {
            \Logger::getLogger("service")->error($e->getMessage());
            \Logger::getLogger("service")->error($e->getTraceAsString());
        }

        return [];
    }

    /**
     * Store international products from report summary
     *
     * @param  array $products  An array international price products
     *                          [<productName>, <qty>, <unitPrice>]
     * @return array            An array of id
     */
    protected function storeIntlProducts($products)
    {
        $baseAttribute = array_merge($this->attributes(), [
            'useReport'  => 0,
            'reportName' => '',
            'productId'  => null,
            'unitPrice'  => 0,
            'qty'        => 0,
        ]);
        $results = [];

        foreach ($products as $product)
        {
            $product = new static(array_merge($baseAttribute, [
                'productName' => $this->productName . ' (' . ucwords(strtolower($product[0])) . ')',
                'qty'         => $this->toFloat($product[1]),
                'unitPrice'   => $this->toFloat($product[2]),
            ]));
            $results[] = $product->save();
        }

        return $results;
    }

    /**
     * Get International prices summary
     *
     * @param  \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet &$sheet Instance of worksheet
     * @return array
     */
    protected function getIntlSummaryValues(&$sheet)
    {
        if (!$rowNumber = $this->getRowNumber($sheet, \ApiReport::INTL_PRICE_SUMMARY_TITLE, 'A', 1))
        {
            throw new Exception("International Column Not Found");
        }

        if (!$totalRow = $this->getRowNumber($sheet, \ApiReport::INTL_PRICE_SUMMARY_TOTAL, 'A', $rowNumber, false))
        {
            throw new Exception("Total International Price Not Found");
        }

        $values = $sheet->rangeToArray('A' . ($rowNumber + 2) . ':F' . ($totalRow - 1));

        return array_map(function ($item)
        {
            return array_values(array_filter($item));
        }, $values);

    }

    /**
     * Get row number that match with given content
     *
     * @param  PhpOffice\PhpSpreadsheet\Worksheet\Worksheet  &$sheet
     * @param  string  $content
     * @param  string  $column
     * @param  integer $startRow
     * @param  boolean $skipEmpty
     * @return integer
     */
    protected function getRowNumber(&$sheet, $content, $column = 'A', $startRow = 1, $skipEmpty = true)
    {
        // Set the max row that will check the value
        $maxRow = $startRow + 100;

        while (($value = $sheet->getCell($column . $startRow)->getValue()) !== $content)
        {
            // Stop the iteration If it is not skipEmpty mode and the value is empty
            // Or iteration is more than maxRow
            // It will return 0 that indicate the content is not found
            if (($skipEmpty === false && empty($value)) || $startRow > $maxRow)
            {
                return 0;
            }

            $startRow++;
        }

        return $startRow;
    }

    /**
     * Convert string of price to float
     *
     * @param String $price  Price value that come from billing report, ex: 1,234.99
     * @return float         Result is a float value, ex: 1234.99
     */
    protected function toFloat($price)
    {
        return floatval(str_replace(",", "", $price));
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
     * @return array|int
     */
    public function updateProduct($key, array $data)
    {
        if (!$model = $this->find($key))
        {
            throw new Exception("Product Not Found");
        }

        $data['updateAt'] = date('Y-m-d H:i:s');

        $newData = array_intersect_key($data, $model->attributes());

        $this->attributes = array_merge($this->attributes, $model->attributes(), $newData);

        if ($this->isHistory() && intval($this->useReport) === 1)
        {
            return $this->setQtyAndUnitPriceFromReport();
        }

        return $this->update($this->attributes);
    }

    /**
     * Get amount product
     *
     * @return  float
     */
    public function amount()
    {
        $qty       = intval($this->qty);
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
