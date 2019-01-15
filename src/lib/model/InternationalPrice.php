<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\ModelContract;

/**
 * Model for INTERNATIONAL_PRICE table
 * This model use to get and store international price data
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InternationalPrice extends ModelContract
{
    /**
     * Default Price Country Code
     *
     * @var String
     */
    const DEFAULT_PRICE_COUNTRY_CODE = "ID";

    /**
     * Table name of invoice profile
     *
     * @var string
     */
    protected $tableName = DB_BILL_PRICELIST . '.BILLING_INTERNATIONAL_PRICE';

    /**
     * Primary key of invoice profile
     *
     * @var string
     */
    protected $primaryKey = 'BILLING_INTERNATIONAL_PRICE_ID';

    /**
     * Get all International Price value
     *
     * @return array
     */
    public function all()
    {
        $baseQuery         = "SELECT * FROM {$this->tableName} JOIN " . DB_SMS_API_V2 . ".COUNTRY ON COUNTRY.COUNTRY_CODE_REF = {$this->tableName}.COUNTRY_CODE_REF";
        $queryDefaultPrice = $baseQuery . " WHERE {$this->tableName}.COUNTRY_CODE_REF = '" . self::DEFAULT_PRICE_COUNTRY_CODE . "' LIMIT 1";
        $queryOtherPrices  = $baseQuery . " WHERE {$this->tableName}.COUNTRY_CODE_REF != '" . self::DEFAULT_PRICE_COUNTRY_CODE . "' ORDER BY COUNTRY_NAME";
        $defaultPrice      = $this->select($queryDefaultPrice)->fetch();

        if (empty($defaultPrice))
        {
            if ($this->insertDefaultValue())
            {
                $defaultPrice = $this->select($queryDefaultPrice)->fetch();
            }

        }

        $otherPrices = $this->select($queryOtherPrices)->fetchAll();

        // Prepend default prices to otherPrices
        array_unshift($otherPrices, $defaultPrice);

        return $otherPrices;
    }

    /**
     * Create default value if it doesn't exists
     *
     * @return void
     */
    protected function insertDefaultValue()
    {
        $model = new self();

        $model->countryCodeRef = self::DEFAULT_PRICE_COUNTRY_CODE;
        $model->unitPrice      = 1000;

        return $model->save();
    }

}
