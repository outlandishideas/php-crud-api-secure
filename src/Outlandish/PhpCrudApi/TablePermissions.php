<?php

namespace Outlandish\PhpCrudApi;

abstract class TablePermissions
{
    /** @var array Singletons for each subclass of TablePermissions, keyed by class name */
    protected static $singletons = [];


    protected $tableName;

    /** @var array Default values columns that can be accessed in read operations (read, list) */
    protected $allReadColumns = [];

    /** @var array Default values columns that can be accessed in write operations (create, update, increment, delete) */
    protected $allWriteColumns = [];

    /** @var array Columns for which the values can be output in read operations from this table */
    protected $readColumns = [];

    /** @var array Columns for which the values can be output in list operations on this table */
    protected $listColumns = [];

    /** @var array Columns for which the values can be updated to by update operations on this table */
    protected $updateColumns = [];

    /** @var array Columns that can be read from this table */
    protected $incrementColumns = [];

    /** @var array Columns that can be written to in a create operation */
    protected $createColumns = [];

    /** @var bool Whether to allow delete operations on this table */
    protected $canDelete = false;

    protected function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    final public static function getInstance()
    {
        $className = static::class;
        if (!isset(self::$singletons[$className])) {
            self::$singletons[$className] = new static();
        }

        return self::$singletons[$className];
    }

    /**
     * The name of this table in the database
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Columns from this table that can be accessed by `read` operations
     * @return array
     */
    public function getReadColumns(): array
    {
        return $this->readColumns ?: $this->allReadColumns;
    }


    /**
     * Columns from this table that can be accessed in `list` operations
     * @return array
     */
    public function getListColumns(): array
    {
        return $this->listColumns ?: $this->allReadColumns;
    }

    /**
     * Columns from this table that can be written to in `create` operations
     * @return array
     */
    public function getCreateColumns(): array
    {
        return $this->createColumns ?: $this->allWriteColumns;
    }

    /**
     * Columns from this table that can be written to in `update` operations
     * @return array
     */
    public function getUpdateColumns(): array
    {
        return $this->updateColumns ?: $this->allWriteColumns;
    }

    /**
     * Whether `delete` operations are allowed on records in this table
     * @return bool
     */
    public function allowDelete(): bool
    {
        return $this->canDelete;
    }

    /**
     * Columns from this table that can be incremented with `incremented` operations
     * @return array
     */
    public function getIncrementColumns(): array
    {
        return $this->incrementColumns ?: $this->allWriteColumns;
    }


    /**
     * Output the permissions from this table as an array in the format of `['operation'] => ['allowedColumn1', 'allowedColumn2']`
     * @return array[]|bool[]
     */
    final public function toArray(): array
    {
        return [
            'read' => $this->getReadColumns(),
            'list' => $this->getListColumns(),
            'create' => $this->getCreateColumns(),
            'update' => $this->getUpdateColumns(),
            'increment' => $this->getIncrementColumns(),
            'delete' => $this->allowDelete(),
        ];
    }

    final public function isPermitted($operation, $columnName): bool
    {
        $permissions = $this->toArray();
        if (array_key_exists($operation, $permissions)) {
            if (!$columnName) {
                // this is a table-based operation such as delete and we don't care about columns
                return true;
            } else {
                // the column has been explicitly allowed
                return in_array($columnName, $permissions[$operation]);
            }
        }
        return false;
    }
}
