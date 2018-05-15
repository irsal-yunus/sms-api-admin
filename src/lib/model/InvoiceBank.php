<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use Firstwap\SmsApiAdmin\lib\model\ModelContract;
use \Exception;

/**
 * Model for INVOICE_BANK table
 * This model use to get and update bank for invoice
 *
 * @author Muhammad Rizal <muhammad.rizal@1rstwap.com>
 */
class InvoiceBank extends ModelContract
{
    /**
     * Table name of invoice profile
     *
     * @var string
     */
    protected $tableName = 'INVOICE_BANK';

    /**
     * Primary key of invoice profile
     *
     * @var string
     */
    protected $primaryKey = 'BANK_ID';

    /**
     * Get all Bank value
     *
     * @return array
     */
    public function all()
    {
        return $this->select("SELECT * from $this->tableName order by {$this->primaryKey} desc")->fetchAll();
    }

    /**
     * Perform update bank
     *
     * @param string $key
     * @param  array $data
     * @return void
     */
    public function updateBank($key, array $data)
    {
        if (!$model = $this->find($key)) {
            throw new Exception("Bank Not Found");
        }

        $model->update($data);
    }

    /**
     * validate account number is duplicate or not
     *
     * @param String $accountNumber
     * @param mixed $bankId
     * @return  bool
     */
    public function isAccountNumberDuplicate($accountNumber, $bankId = null)
    {
        $query = "SELECT count(1) from {$this->tableName} where ACCOUNT_NUMBER = '{$accountNumber}'";

        if ($bankId) {
            $query .= " AND {$this->primaryKey} != $bankId";
        }

        return $this->select($query)->fetchColumn() > 0;
    }
}
