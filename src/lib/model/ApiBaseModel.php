<?php
/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

/**
 * Description of ApiBaseModel
 *
 * @author setia.budi
 */
abstract class ApiBaseModel
{
    /**
     *
     * @var Logger
     */
    protected $logger;
    public static $cacheDir = '';

    public function __construct()
    {
        $this->logger = Logger::getLogger(get_class($this));
    }

    /**
     * Generate where clause based on rules
     * @param array $rules
     * @param array $fields array of ruleName=>value
     * @return string
     */
    protected static function buildDynamicWhereClause(array $rules, array $fields, $useWhere = false)
    {
        if (!$rules) {
            return null;
        }

        $sql = '';
        $conditions = array();
        foreach ($rules as $fieldID => $definition) {
            if (!isset($fields[$fieldID])) {
                continue;
            }

            if ($definition['param'] === false) {
                $true = filter_var($fields[$fieldID], FILTER_VALIDATE_BOOLEAN, array('flags' => FILTER_NULL_ON_FAILURE));
                if ($true === null) {
                    trigger_error('Invalid boolean values', E_USER_WARNING);
                    continue;
                }
                $boolValue = $true ? '1' : '0';
                $conditions[$fieldID] = "{$definition['field']}=$boolValue";
            } else {
                $conditions[$fieldID] = $definition['field'] . '=' . $definition['param'];
            }

        }
        if (!$conditions) {
            return null;
        }

        $query = implode(' and ', $conditions);
        if ($query == '') {
            return null;
        }

        return $useWhere ? " where $query " : $query;
    }
    protected static function buildDynamicUpdateClause(array $rules, array $fields)
    {
        if (!$rules) {
            return null;
        }

        $updates = array();
        foreach ($rules as $fieldID => $definition) {
            if (!isset($fields[$fieldID])) {
                continue;
            }

            if ($definition['param'] === false) {
                continue;
            }

            $updates[$fieldID] = $definition['field'] . '=' . $definition['param'];
        }
        if (!$updates) {
            return null;
        }

        return implode(',', $updates);
    }

    protected static function bindDynamicValues(array $rules, array $values, PDOStatement $statement)
    {
        if (!$rules) {
            return;
        }

        foreach ($rules as $fieldID => $definition) {
            if ($definition['param'] === false) {
                continue;
            }

            if (!isset($values[$fieldID])) {
                if (!isset($definition['default'])) {
                    continue;
                }

                $value = $definition['default'];
                $type = isset($definition['defaultType']) ?
                $definition['defaultType'] :
                (isset($definition['type']) ? $definition['type'] : PDO::PARAM_STR);
            } elseif (isset($rules['emptyValue']) && empty($values[$fieldID])) {
                $value = $definition['emptyValue'];
                $type = isset($definition['emptyType']) ?
                $definition['emptyType'] :
                (isset($definition['type']) ? $definition['type'] : PDO::PARAM_STR);
            } else {
                $value = $values[$fieldID];
                $type = isset($definition['type']) ? $definition['type'] : PDO::PARAM_STR;
            }
            $statement->bindValue($definition['param'], $value, $type);
        }
    }

}
