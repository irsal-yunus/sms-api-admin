<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Exception;
use Firstwap\SmsApiAdmin\lib\model\ModelContract;

/**
 * Model for INVOICE_SETTING table
 * This model use to get and update setting for invoice
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InvoiceSetting extends ModelContract
{
    /**
     * Database connection name that setup in
     * configs/database.ini
     *
     * @var PDO
     */
    protected $connection = 'invoice';

    /**
     * Table name for invoice setting
     *
     * @var string
     */
    protected $tableName = DB_INVOICE.'.INVOICE_SETTING';

    /**
     * Primary key for invoice setting
     *
     * @var string
     */
    protected $primaryKey = 'SETTING_ID';

    /**
     * Setting value
     *
     * @var stdClass
     */
    protected static $setting = null;

    /**
     * Get Setting value
     *
     * @return InvoiceSetting|null
     */
    public function getSetting()
    {
        if (empty(self::$setting)) {
            if (!$setting = $this->selectSetting()) {
                $setting = $this->initialSetting();
            }

            self::$setting = $setting;
        }

        return self::$setting;
    }

    /**
     * Clear setting cache
     *
     * @return void
     */
    public function clearCache()
    {
        self::$setting = null;
    }

    /**
     * Perform select setting
     *
     * @return InvoiceSetting|null
     */
    public function selectSetting()
    {
        $query = "SELECT * FROM {$this->tableName}
            ORDER BY {$this->primaryKey} DESC
            LIMIT 1";

        return self::$setting = $this->select($query)->fetch() ?: null;
    }

    /**
     * Perform update setting
     *
     * @param  array $data
     * @return boolean
     */
    public function updateSetting(array $data)
    {
        if (!$setting = $this->getSetting()) {
            throw new Exception("Setting Not Found");
        }

        if ($setting->update($data)) {
            self::$setting = null;
            return self::$setting = $this->getSetting();
        }

        throw new Exception("Failed Update Setting");
    }

    /**
     * Initialize data setting if doesn't exists
     *
     * @return boolean
     */
    protected function initialSetting()
    {
        $data = [
            'paymentPeriod' => '14',
            'authorizedName' => 'Mona Eftarina',
            'lastInvoiceNumber' => 0,
            'authorizedPosition' => 'Finance & Accounting Manager',
            'noteMessage' => "Please quote the above invoice number reference on all payment orders and note that all associated charges for the financial transfer are at the payees expense.\n\nAny errors/discrepancies must be reported to PT. FIRST WAP INTERNATIONAL (financial@1rstwap.com) in writing withing 7 (seven) days, otherwise claims for changes will not be accepted",
            'invoiceNumberPrefix' => '1rstwap - ',
        ];

        if ($this->insert($data)) {
            return $this->selectSetting();
        }

        throw new Exception("Failed Initialize Setting");
    }

    /**
     * Refresh Invoice Number
     *
     * @return void
     */
    public function refreshInvoiceNumber()
    {
        $query = "UPDATE {$this->tableName} SET
                    `LAST_INVOICE_NUMBER` = IFNULL(
                    (SELECT INVOICE_NUMBER
                    FROM ".DB_INVOICE.".INVOICE_HISTORY
                    ORDER BY INVOICE_NUMBER DESC
                    LIMIT 1), 0)";

        $this->db->prepare($query)->execute();
    }
}
