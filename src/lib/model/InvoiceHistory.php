<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\InvoiceProduct;
use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use \Exception;

/**
 * Model for INVOICE_HISTORY table
 * This model use to get and update History for invoice
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InvoiceHistory extends ModelContract
{
    /**
     * Table name of invoice history
     *
     * @var string
     */
    protected $tableName = 'INVOICE_HISTORY';

    /**
     * Primary key of invoice history
     *
     * @var string
     */
    protected $primaryKey = 'INVOICE_ID';

    /**
     * Get all History value
     *
     * @return array
     */
    public function all()
    {
        return $this->select("SELECT * from $this->tableName order by {$this->primaryKey} desc")->fetchAll();
    }

    /**
     * Get all History with product value
     *
     * @param string $historyId
     * @return array
     */
    public function withProduct($historyId = null)
    {
        if (is_null($historyId)) {
            $data = $this->all();
        } else {
            if (!$model = $this->find($historyId)) {
                throw new Exception("History Not Found");
            }
            $data = [$model];
        }

        $this->loadProduct($data);

        return $data;
    }

    /**
     * Load product for historys
     *
     * @param array $data
     * @return array
     */
    public function loadProduct(array &$data)
    {
        $historyIds = array_column($data, $this->keyName());
        $products = $this->getProduct($historyIds);
        $products = $this->groupBy($products, 'ownerId');

        foreach ($data as &$item) {
            $item['products'] = $products[$item->key()] ?? null;
        }
    }

    /**
     * Get product that belongsto history
     *
     * @param string|array $historyId
     * @return array
     */
    public function getProduct($historyId)
    {
        if (empty($historyId)) {
            return [];
        }

        return $this->product()->history($historyId);
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
     * Perform update History
     *
     * @param string $key
     * @param  array $data
     * @return void
     */
    public function updateHistory($key, array $data)
    {
        if (!$model = $this->find($key)) {
            throw new Exception("History Not Found");
        }

        $data['updateAt'] = date('Y-m-d H:i:s');

        $model->update($data);
    }
}
