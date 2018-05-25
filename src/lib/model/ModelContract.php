<?php

namespace Firstwap\SmsApiAdmin\lib\model;

use ArrayAccess;
use JsonSerializable;
use \Exception;
use \PDO;
use \SmsApiAdmin;

abstract class ModelContract implements ArrayAccess, JsonSerializable
{
    /**
     * Table name for invoice setting
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * Database connection instance
     *
     * @var PDO
     */
    protected $db = null;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The database transaction status
     *
     * @var array
     */
    protected $transactionStatus = false;

    /**
     * Constructor for ApiInvoiceSetting class
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->db = $this->getPdo();

        if (!empty($attributes)) {
            $this->attributes = $attributes;
        }
    }

    /**
     * Get primary key model
     *
     * @param bool $isCamelCase
     * @return String
     */
    public function keyName($isCamelCase = true)
    {
        if ($isCamelCase) {
            return $this->camelCase($this->primaryKey);
        }

        return $this->primaryKey;
    }

    /**
     * Get primary key value
     *
     * @return String
     */
    public function key()
    {
        return $this->{$this->keyName()};
    }

    /**
     * Set primary key value
     *
     * @param  mixed $value
     * @return void
     */
    public function setKey($value)
    {
        $this->{$this->keyName()} = $value;
    }

    /**
     * Get table name
     *
     * @return  string
     */
    public function tableName()
    {
        return $this->tableName;
    }

    /**
     * Perform Select Query
     *
     * @param  string $query
     * @return array
     */
    public function select($query)
    {
        return $this->db->query($query, PDO::FETCH_CLASS, get_called_class());
    }

    /**
     * Perform Select Query
     *
     * @param  mixed $keyValue
     * @return mixed
     */
    public function find($keyValue)
    {
        $query = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = {strval($keyValue)}";

        return $this->select($query)->fetch();
    }

    /**
     * Perform Update action
     *
     * @param  array $data
     * @return bool
     */
    public function update(array $data)
    {
        if ($this->key() === null) {
            throw new Exception("Can't perform Update, No primaryKey value");
        }

        $values = array_intersect_key($data, $this->attributes);

        if (array_key_exists($this->keyName(), $values)) {
            // Remove primary key from values to prevent update primary key
            unset($values[$this->keyName()]);
        }

        $bindParam = $this->getUpdateParam($values);

        $query = "UPDATE {$this->tableName()} SET $bindParam WHERE {$this->keyName(false)} = :primaryKey";

        $stmt = $this->db->prepare($query);

        $this->bindValue($stmt, $values);
        $stmt->bindValue(':primaryKey', $this->key(), PDO::PARAM_INT);

        if (!$updated = $stmt->execute()) {
            throw new Exception("Failed Update " . json_encode($stmt->errorInfo()[2]));
        }

        $this->attributes = array_merge($this->attributes, $values);

        return $updated;
    }

    /**
     * Get PDO instance
     *
     * @return  PDO
     */
    protected function getPdo()
    {
        return SmsApiAdmin::getDB(SmsApiAdmin::DB_SMSAPI);
    }

    /**
     * Set param for update
     *
     * @param array $data
     * @return String
     */
    protected function getUpdateParam(array $data)
    {
        $query = [];

        foreach ($data as $key => $value) {
            $query[] = "{$this->upperSnakeCase($key)} = :$key";
        }

        return implode(', ', $query);
    }

    /**
     * Perform insert action
     *
     * @param  array $data
     * @return array
     */
    public function insert(array $data)
    {
        $params = $this->getInsertParam($data);

        $query = "INSERT INTO {$this->tableName}({$params['columns']}) VALUES ({$params['values']})";

        $stmt = $this->db->prepare($query);
        $this->bindValue($stmt, $data);

        if (!$stmt->execute()) {
            throw new Exception("Failed Insert " . json_encode($stmt->errorInfo()[2]));
        }

        return $this->db->lastInsertId();
    }

    /**
     * Save model instance to database
     *
     * @param array $newAttributes Pass an array data to replace current attributes
     * @param bool
     */
    public function save(array $newAttributes = [])
    {
        $this->attributes = array_merge($this->attributes, $newAttributes);

        if (empty($this->key())) {
            return $this->insert($this->attributes);
        }

        return $this->update($this->attributes);
    }

    /**
     * Group by spesific column
     *
     * @param array $data
     * @param string $columnName
     * @return  array
     */
    public function groupBy(array $data, $columnName)
    {
        $arr = [];

        if (empty($data)) {
            return $arr;
        }

        if (!array_key_exists($columnName, current($data)->attributes())) {
            throw new Exception("Column name doesn't exists");
        }

        foreach ($data as $key => $item) {
            if ($value = $item[$columnName]) {
                $arr[$value][$key] = $item;
            }
        }

        return $arr;
    }

    /**
     * Set param for insert
     */
    protected function getInsertParam(array $data)
    {
        $columns = [];
        $values = [];

        foreach ($data as $key => $value) {
            $columns[] = $this->upperSnakeCase($key);
            $values[] = ":$key";
        }

        return [
            'columns' => implode(', ', $columns),
            'values' => implode(', ', $values),
        ];
    }

    /**
     * Perform delete action
     *
     * @return void
     */
    public function delete($key = null)
    {
        if (is_null($key) && !$key = $this->key()) {
            throw new Exception("Can't perform delete, No primary key value");
        }

        if (!$model = $this->find($key)) {
            throw new Exception("Data Not Found");
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :primaryKey");
        $stmt->bindValue(':primaryKey', $key);

        if (!$deleted = $stmt->execute()) {
            throw new Exception("Failed Delete " . json_encode($stmt->errorInfo()[2]));
        }

        return $deleted;
    }

    /**
     * Give value for param query
     *
     * @param  PDOStatement &$PDOStatement
     * @param  array $values        [description]
     * @return void
     */
    protected function bindValue(&$PDOStatement, &$values)
    {
        foreach ($values as $key => $value) {
            $PDOStatement->bindValue(":$key", $value, PDO::PARAM_STR);
        }
    }

    /**
     * Perform Transaction for query
     *
     * @return void
     */
    public function beginTransaction()
    {
        if ($this->db->inTransaction() === false) {
            $this->db->beginTransaction();
        }
    }

    /**
     * Perform rollBack for query
     *
     * @return void
     */
    public function rollBack()
    {
        if ($this->db->inTransaction() === true) {
            $this->db->rollBack();
        }
    }

    /**
     * Perform commit for query
     *
     * @return void
     */
    public function commit()
    {
        if ($this->db->inTransaction() === true) {
            $this->db->commit();
        }
    }

    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    public function camelCase($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', strtolower($value)));

        return lcfirst(str_replace(' ', '', $value));
    }

    /**
     * Convert a value to upper camel case.
     *
     * @param  string  $value
     * @return string
     */
    public function upperSnakeCase($value)
    {
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->attributes, $options);
    }

    /**
     * Get attributes of model
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (array_key_exists($offset, $this->attributes)) {
            unset($this->attributes[$offset]);
        } else {
            unset($this->$offset);
        }
    }

    /**
     * Json serialize implements
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->attributes();
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            return $this->{$key} = $value;
        }

        if ($key{0} === strtoupper($key{0})) {
            $key = $this->camelCase($key);
        }

        $this->attributes[$key] = $value;
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
