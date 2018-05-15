<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use Exception;

/**
 * Model for INVOICE_SETTING table
 * This model use to get and update setting for invoice
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InvoiceSetting extends ModelContract
{
    /**
     * Table name for invoice setting
     *
     * @var string
     */
    protected $tableName = 'INVOICE_SETTING';

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
            if (! $setting = $this->selectSetting()) {
                $setting = $this->initialSetting();
            }

            self::$setting = $setting;
        }

        return self::$setting;
    }

    /**
     * Perform select setting
     *
     * @return InvoiceSetting|null
     */
    protected function selectSetting()
    {
        return $this->select("SELECT * from $this->tableName order by $this->primaryKey desc limit 1")->fetch();
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
            'authorizedName' => '',
            'authorizedPosition' => '',
            'approvedName' => '',
            'approvedPosition' => '',
            'noteMessage' => '',
            'invoiceNumberPrefix' => '1rstwap - #number#',
        ];

        if ($this->insert($data)) {
            return $this->selectSetting();
        }

        throw new Exception("Failed Initialize Setting");
    }
}
