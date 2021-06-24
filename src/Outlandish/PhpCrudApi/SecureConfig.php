<?php

namespace Outlandish\PhpCrudApi;

use Tqdev\PhpCrudApi\Config;


class SecureConfig extends Config
{
    protected $_values;
    protected $_table_column_mapping;

    /**
     * SecureConfig constructor.
     * @param array $values Accepts the same keys as  `Tqdev\PhpCrudApi\Config::__construct()`
     * @param null|array[][] $table_column_mapping either a 2D array like `[tablename] => [col1, col2]` (for read-only)
     *              or a 3D array containing `[read] => [tablename] => [col1, col2], [write] => [tablename] => [col1]`
     * @throws \Exception
     */
    public function __construct(array $values, $table_column_mapping = null)
    {
        $this->_values = $values;
        $this->_table_column_mapping = $table_column_mapping;
        // if $table_column_mapping has been supplied we'll automatically enable the `authorization` middleware and
        // provide the `tableHandler` and `columnHandler` middleware functions
        if (is_array($table_column_mapping)) {

            if (!is_array($this->_table_column_mapping) || !is_array(current($this->_table_column_mapping))) {
                throw new \InvalidArgumentException('`$table_column_mapping` 
                must be a 2D array in the format [tablename] => [col1, col2]
                or a 3D array containing [read] => [tablename] => [col1, col2]');
            }
            if (!is_array(current(current($this->_table_column_mapping)))) {
                $this->_table_column_mapping = ['read' => $this->_table_column_mapping, 'write' => []];
            }

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
        //work out which set of permissions to use
        if (in_array($operation, ['list', 'read'])) {
            $permissions = $this->_table_column_mapping["read"];
        } else {
            $permissions = $this->_table_column_mapping["write"];

            // If the request is trying a write operation it should have some sort of  authentication
            // the authApiKey middleware sets the supplied API-KEY as $_SESSION['API_KEY'] if it is valid

            //todo: make this work with different auth middlewares
            //todo: return a valid PSR7 response
            if(!isset($_SESSION) || !isset($_SESSION['API_KEY'])){
                throw new \Exception("valid X-API-KEY header must be supplied for write operations" );
            }
        }

        if (in_array($tableName, array_keys($permissions))) {
            if (!$columnName) { //we're just checking if the table is allowed
                return true;
            } elseif (in_array($columnName, $permissions[$tableName])) { //the column has been explicitly allowed
                return true;
            }
        }

        return false;
    }

}