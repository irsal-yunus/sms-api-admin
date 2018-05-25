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
    protected $tableName = 'INVOICE_PROFILE';

    /**
     * Primary key of invoice profile
     *
     * @var string
     */
    protected $primaryKey = 'PROFILE_ID';

    /**
     * Get all Profile value
     *
     * @return array
     */
    public function all()
    {
        return $this->select("SELECT {$this->tableName}.*, CLIENT.CUSTOMER_ID, CLIENT.COMPANY_NAME, INVOICE_BANK.BANK_NAME from {$this->tableName}
             LEFT JOIN CLIENT on CLIENT.CLIENT_ID = {$this->tableName}.CLIENT_ID
             LEFT JOIN INVOICE_BANK on INVOICE_BANK.BANK_ID = {$this->tableName}.BANK_ID
             order by CLIENT.CUSTOMER_ID ASC"
        )->fetchAll();
    }

    /**
     * Perform Select Query
     *
     * @param  mixed $keyValue
     * @return mixed
     */
    public function find($keyValue)
    {
        $query = "SELECT {$this->tableName}.*, CLIENT.*, INVOICE_BANK.* from {$this->tableName}
             LEFT JOIN CLIENT on CLIENT.CLIENT_ID = {$this->tableName}.CLIENT_ID
             LEFT JOIN INVOICE_BANK on INVOICE_BANK.BANK_ID = {$this->tableName}.BANK_ID
             WHERE {$this->primaryKey} = {strval($keyValue)}";

        return $this->select($query)->fetch();
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

        $query = "SELECT * FROM USER
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

        $this->loadProduct($data);

        return $data;
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
            $item['products'] = $products[$item->key()] ?? null;
        }
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

        $data['updateAt'] = date('Y-m-d H:i:s');

        $model->update($data);
    }

    /**
     * validate client is duplicate or not
     *
     * @param String $clientId
     * @param mixed $profileId
     * @return  bool
     */
    public function isClientDuplicate($clientId, $profileId = null)
    {
        $query = "SELECT count(1) from $this->tableName where CLIENT_ID = '{$clientId}'";

        if ($profileId) {
            $query .= " AND {$this->primaryKey} != $profileId";
        }

        return $this->select($query)->fetchColumn() > 0;
    }
}
