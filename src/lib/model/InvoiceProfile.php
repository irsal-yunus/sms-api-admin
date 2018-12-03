<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use \Exception;

/**
 * Model for INVOICE_PROFILE table
 * This model use to get and update profile for invoice
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InvoiceProfile extends ModelContract
{
    /**
     * Table name of invoice profile
     *
     * @var string
     */
    protected $tableName = DB_INVOICE.'.INVOICE_PROFILE';

    /**
     * Primary key of invoice profile
     *
     * @var string
     */
    protected $primaryKey = 'PROFILE_ID';


    /**
     * Get all Profile data
     *
     * @param Integer $includeArchived
     * @return array
     */
    public function all($includeArchived = false)
    {
        $query = $this->defaultQuery();

        if ($includeArchived === false) {
            $query .= " WHERE ARCHIVED_DATE IS NULL ";
        }

        $query .=  ' ORDER BY CLIENT.COMPANY_NAME ASC, PROFILE_NAME ASC';

        return $this->select($query)->fetchAll();
    }

    /**
     * Perform Select Query
     *
     * @param  mixed $keyValue
     * @return mixed
     */
    public function find($keyValue)
    {
        $query = "{$this->defaultQuery()} WHERE {$this->primaryKey} = {strval($keyValue)} LIMIT 1";

        return $this->select($query)->fetch();
    }

    /**
     * Get profile data for auto generate invoice
     * only profile that doesn't have invoice for current month
     * profile data also has the products data
     *
     * @return array
     */
    public function getProfileForAutoGenerate()
    {
        $firstDate = date('Y-m-d', strtotime('first day of this month'));
        $query = "{$this->defaultQuery()} WHERE AUTO_GENERATE = 1
            AND ARCHIVED_DATE is null
            AND {$this->tableName}.{$this->primaryKey} NOT IN
                (SELECT INVOICE_HISTORY.{$this->primaryKey} FROM INVOICE_HISTORY WHERE START_DATE >= '$firstDate')
            ORDER BY CLIENT.COMPANY_NAME ASC";
        $profiles = $this->select($query)->fetchAll();

        return $this->loadProduct($profiles);
    }

    /**
     * Get default queries for select action
     *
     * @return string
     */
    protected function defaultQuery()
    {
        return "SELECT {$this->tableName}.*, CLIENT.*, INVOICE_BANK.* from {$this->tableName}
            LEFT JOIN ".DB_SMS_API_V2.".CLIENT on ".DB_SMS_API_V2.".CLIENT.CLIENT_ID = {$this->tableName}.CLIENT_ID
            LEFT JOIN ".DB_INVOICE.".INVOICE_BANK on INVOICE_BANK.BANK_ID = {$this->tableName}.BANK_ID";
    }

    /**
     * Get user API that client have
     *
     * @return  array
     */
    public function loadApiUsers()
    {
        if (empty($this->clientId)) {
            throw new Exception("Client ID is empty");
        }

        $query = "SELECT * FROM ".DB_SMS_API_V2.".USER
            WHERE CLIENT_ID = {$this->clientId}
            ORDER BY USER_NAME ASC";

        return $this->apiUsers = $this->select($query)->fetchAll();
    }

    /**
     * Get all Profile with product value
     *
     * @param string $profileId
     * @return array
     */
    public function withProduct($profileId = null)
    {
        if (is_null($profileId)) {
            $data = $this->all();
        } else {
            if (!$model = $this->find($profileId)) {
                throw new Exception("Profile Not Found");
            }
            $data = [$model];
        }

        return $this->loadProduct($data);
    }

    /**
     * Load product for profiles
     *
     * @param array $data
     * @return array
     */
    public function loadProduct(array &$data)
    {
        $profileIds = array_column($data, $this->keyName());
        $products = $this->getProduct($profileIds);
        $products = $this->groupBy($products, 'ownerId');

        foreach ($data as &$item) {
            $item['products'] = $products[$item->key()] ?? [];
        }

        return $data;
    }

    /**
     * Get product that belongsto profile
     *
     * @param string|array $profileId
     * @return array
     */
    public function getProduct($profileId)
    {
        if (empty($profileId)) {
            return [];
        }

        return $this->product()->profile($profileId);
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
     * Perform update profile
     *
     * @param string $key
     * @param  array $data
     * @return void
     */
    public function updateProfile($key, array $data)
    {
        if (!$model = $this->find($key)) {
            throw new Exception("Profile Not Found");
        }

        $data['updatedAt'] = date('Y-m-d H:i:s');

        $model->update($data);
    }

    /**
     * validate profile name is duplicate or not
     *
     * @param String $profileName
     * @param mixed $profileId
     * @return  bool
     */
    public function isProfileNameDuplicate($profileName, $profileId = null)
    {
        $query = "SELECT count(1) from $this->tableName where PROFILE_NAME = '{$profileName}'";

        if ($profileId) {
            $query .= " AND {$this->primaryKey} != $profileId";
        }

        return $this->select($query)->fetchColumn() > 0;
    }
}
