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
     * @param  $archived int
     * @return array
     */
    public function all($archived)
    {
        $query = $this->queryAll($archived);
        return $this->select($query)->fetchAll();
    }

    /**
     * Get Query for all based on archived status
     *
     * @param  $archived int
     * @param  $select  string
     * @return array
     */
    public function queryAll($archived,$select){
        $archived = ($archived===null) ? "WHERE ARCHIVED_DATE is null" : "";
        $query="{$this->defaultQuery($select)} {$archived} ORDER BY CLIENT.COMPANY_NAME ASC";
        return $query;
    }

    /**
     * Get Profile data by page
     *
     * @param  $archived int
     * @param $page int
     * @return array
     */
    public function getProfilebyPage($archived=null,$page=1){
        $chunk  = LIMIT_PER_PAGE;
        $offset = ($page - 1) * ($chunk);
        $totalQuery  = $this->queryAll($archived,"count(1)");

        $query       = $this->queryAll($archived);
        $query      .= " LIMIT {$chunk} OFFSET {$offset} ";

        $data       = $this->select($query)->fetchAll();
        $totalData  = $this->select($totalQuery)->fetchColumn();

        return [
            'data' => $data,
            'total' => $totalData
        ];
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
     * @param String $select
     * @return string
     */
    protected function defaultQuery($select="*")
    {
        return "SELECT {$select} from {$this->tableName}
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
