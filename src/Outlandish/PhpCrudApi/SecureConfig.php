<?php

namespace Outlandish\PhpCrudApi;

use Tqdev\PhpCrudApi\Config;
use Outlandish\PhpCrudApi\TablePermissions;


class SecureConfig extends Config
{
    protected $_values = [];
    protected $_table_column_mapping = [];

    /**
     * SecureConfig constructor.
     * @param array $values Accepts the same keys as  `Tqdev\PhpCrudApi\Config::__construct()`
     * @param string[]|null $table_column_mapping
     *              an array of classnames of sub-classes of Outlandish\PhpCrudApi\TablePermissions
     * @throws \Exception
     */
    public function __construct(array $values, array $table_column_mapping = null)
    {
        $this->_values = $values;
        // if $table_column_mapping has been supplied
        // we'll automatically enable the `authorization` middleware and
        // provide the `tableHandler` and `columnHandler` middleware functions
        if (is_array($table_column_mapping)) {
            $firstItem = current($table_column_mapping);
            if (is_subclass_of($firstItem, TablePermissions::class)) {
                foreach ($table_column_mapping as $table) {
                    $this->_table_column_mapping[$table::getTableName()] = $table::toArray();
                }
            } else {
                throw new \InvalidArgumentException('`$table_column_mapping` must be an array of `Outlandish\PhpCrudApi\TablePermissions` sub-classes');
            }

            // Make sure $values is properly initialised
            if (!is_array($values)) {
                $values = [];
            }

            //ensure middlewares key is initialised
            if (!in_array("middlewares", array_keys($values))) {
                $values['middlewares'] = "";
            }

            //add the `authorization` and `pageLimits` middleware if it's not already enabled
            if (!strpos($values['middlewares'], 'authorization')) {
                $middlewares = explode(",", $values['middlewares']);
                $middlewares[] = "authorization";
                $middlewares[] = "pageLimits";
                $values['middlewares'] = implode(",", $middlewares);
            }

            //add a tableHandler based on the $table_column_mapping array (if one is not provided)
            if (!in_array('authorization.tableHandler', array_keys($values))) {
                $values['authorization.tableHandler'] = function ($operation, $tableName) {
                    return $this->isPermitted($operation, $tableName);
                };
            }

            //add a columnHandler based on the $table_column_mapping array (if one is not provided)
            if (!in_array('authorization.columnHandler', array_keys($values))) {
                $values['authorization.columnHandler'] = function ($operation, $tableName, $columnName) {
                    return $this->isPermitted($operation, $tableName, $columnName);
                };
            }
        }

        parent::__construct($values);

        if (!in_array("authorization", array_keys($this->getMiddlewares()))) {
            throw new \InvalidArgumentException("Config must include authorization middleware");
        }

        if (!in_array("tableHandler", array_keys($this->getMiddlewares()['authorization']))) {
            throw new \InvalidArgumentException('Config must include authorization.tableHandler middleware. Use `"authorization.tableHandler" => function ($operation, $tableName){return true;}` to allow all tables');
        }

        if (!in_array("columnHandler", array_keys($this->getMiddlewares()['authorization']))) {
            throw new \InvalidArgumentException('Config must include authorization.tableHandler middleware. Use `"authorization.columnHandler" => function ($operation, $tableName, $columnName){return true;}` to allow all columns');
        }

    }

    private function isPermitted($operation, $tableName, $columnName = null)
    {
        $permissions = $this->_table_column_mapping[$tableName];

        if (in_array($operation, array_keys($permissions))) {
            if (!$columnName) { //this is a table-based operation such as delete and we don't care about columns
                return true;
            } elseif (in_array($columnName, $permissions[$operation])) { //the column has been explicitly allowed
                return true;
            }
        }
        return false;
    }

}