<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\ModelContract;
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
    protected $tableName = 'INVOICE_PRODUCT';

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

        $query = "SELECT * from $this->tableName where OWNER_TYPE = \"$ownerType\" ";

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

        $model->update($data);
    }
}
