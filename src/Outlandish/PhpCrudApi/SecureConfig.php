<?php

namespace Outlandish\PhpCrudApi;

use Tqdev\PhpCrudApi\Config;

class SecureConfig extends Config
{
    /** @var TablePermissions[] */
    protected $tablePermissions = [];

    /**
     * SecureConfig constructor.
     * @param array $values Accepts the same keys as  `Tqdev\PhpCrudApi\Config::__construct()`
     * @param TablePermissions[] $tablePermissions
     *              an array of classnames of sub-classes of Outlandish\PhpCrudApi\TablePermissions
     * @throws \Exception
     */
    public function __construct(array $values, array $tablePermissions = [])
    {
        // Make sure $values is properly initialised
        if (!is_array($values)) {
            $values = [];
        }

        //ensure middlewares key is initialised
        if (!array_key_exists('middlewares', $values)) {
            $values['middlewares'] = '';
        }

        // add the `authorization` and `pageLimits` middleware if it's not already enabled
        $middlewares = explode(',', $values['middlewares']);
        $middlewares[] = 'authorization';
        $middlewares[] = 'pageLimits';
        $values['middlewares'] = implode(',', array_unique($middlewares));

        // if $tablePermissions has been supplied we'll automatically enable the `authorization` middleware and
        // provide the `tableHandler` and `columnHandler` middleware functions
        if (is_array($tablePermissions)) {
            foreach ($tablePermissions as $table) {
                if (!($table instanceof TablePermissions)) {
                    throw new \InvalidArgumentException('`$tablePermissions` must be an array of `Outlandish\PhpCrudApi\TablePermissions` singletons');
                }
                $this->tablePermissions[$table->getTableName()] = $table;
            }

            //add a tableHandler based on the $table_column_mapping array (if one is not provided)
            if (!array_key_exists('authorization.tableHandler', $values)) {
                $values['authorization.tableHandler'] = function ($operation, $tableName) {
                    return $this->isPermitted($operation, $tableName);
                };
            }

            //add a columnHandler based on the $table_column_mapping array (if one is not provided)
            if (!array_key_exists('authorization.columnHandler', $values)) {
                $values['authorization.columnHandler'] = function ($operation, $tableName, $columnName) {
                    return $this->isPermitted($operation, $tableName, $columnName);
                };
            }
        }

        parent::__construct($values);

        $middlewares = $this->getMiddlewares();

        if (!array_key_exists('authorization', $middlewares)) {
            throw new \InvalidArgumentException('Config must include authorization middleware');
        }

        if (!array_key_exists('tableHandler', $middlewares['authorization'])) {
            throw new \InvalidArgumentException('Config must include authorization.tableHandler middleware. Use `"authorization.tableHandler" => function ($operation, $tableName){return true;}` to allow all tables');
        }

        if (!array_key_exists('columnHandler', $middlewares['authorization'])) {
            throw new \InvalidArgumentException('Config must include authorization.tableHandler middleware. Use `"authorization.columnHandler" => function ($operation, $tableName, $columnName){return true;}` to allow all columns');
        }
    }

    private function isPermitted($operation, $tableName, $columnName = null): bool
    {
        if (!array_key_exists($tableName, $this->tablePermissions)) {
            return false;
        }
        return $this->tablePermissions[$tableName]->isPermitted($operation, $columnName);
    }

}
