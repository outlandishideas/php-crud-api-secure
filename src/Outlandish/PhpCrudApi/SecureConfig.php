<?php

namespace Outlandish\PhpCrudApi;

use Tqdev\PhpCrudApi\Config;


class SecureConfig extends Config
{
    public function __construct(array $values, $table_column_mapping = null)
    {
        // if $table_column_mapping has been supplied we'll automatically enable the `authorization` middleware and
        // provide the `tableHandler` and `columnHandler` middleware functions
        if (is_array($table_column_mapping)) {
            if (!is_array($values)) {
                $values = [];
            }

            //ensure middlewares value is initialised
            if (!in_array("middlewares", array_keys($values))) {
                $values['middlewares'] = "";
            }

            //add the authorization middleware if it's not already enabled
            if (!strpos($values['middlewares'], 'authorization')) {
                $middlewares = explode(",", $values['middlewares']);
                $middlewares[] = "authorization";
                $values['middlewares'] = implode(",", $middlewares);
            }

            //add a tableHandler based on the $table_column_mapping array (if one is not provided)
            if (!in_array('authorization.tableHandler', array_keys($values))) {
                $values['authorization.tableHandler'] = function ($operation, $tableName) use ($table_column_mapping) {
                    return in_array($tableName, array_keys($table_column_mapping));
                };
            }

            //add a columnHandler based on the $table_column_mapping array (if one is not provided)
            if (!in_array('authorization.columnHandler', array_keys($values))) {
                $values['authorization.columnHandler'] = function ($operation, $tableName, $columnName) use ($table_column_mapping) {
                    //if the table is not allowed at all, return false
                    if (!in_array($tableName, array_keys($table_column_mapping))) {
                        return false;
                    }
                    //if the column is not allowed from the table, return false
                    return in_array($columnName, $table_column_mapping[$tableName]);
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

}