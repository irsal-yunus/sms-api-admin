<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

/**
 * Description of SmsApiAccount
 *
 * @author setia.budi
 */
class ApiCountry extends ApiBaseModel
{
    /**
     *
     * @return array
     */
    public function getAll()
    {
        try {
            $db          = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
            static $list = null;

            if ($list !== null)
            {
                return $list;
            }

            $query  = 'select COUNTRY_CODE, COUNTRY_NAME from COUNTRY order by COUNTRY_NAME';
            $result = $db->query($query);
            $list   = [];

            $result->setFetchMode(PDO::FETCH_NUM);

            while ($row = $result->fetch())
            {
                $list[$row[0]] = $row[1];
            }

            unset($result);

            return $list;
        }
        catch (PDOException $e)
        {
            $this->logger->error("$e");
            throw new Exception("Query error!");
        }
    }

    /**
     * Get country list for international select input
     *
     * @param  string $countryCodeRef  Country code that will exlude in query
     * @return array
     */
    public static function getInternationalPriceCountry($countryCodeRef = null)
    {
        $db    = SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
        $query = "SELECT COUNTRY.*, BIP.BILLING_INTERNATIONAL_PRICE_ID  FROM COUNTRY
            LEFT JOIN " . DB_BILL_PRICELIST . ".BILLING_INTERNATIONAL_PRICE BIP
                ON BIP.COUNTRY_CODE_REF = COUNTRY.COUNTRY_CODE_REF
            ";

        if ($countryCodeRef !== null)
        {
            $query .= " WHERE COUNTRY.COUNTRY_CODE_REF != '" . $countryCodeRef . "' ";
        }

        $query .= " ORDER BY COUNTRY.COUNTRY_NAME";

        return $db->query($query, PDO::FETCH_OBJ)->fetchAll();
    }
}
