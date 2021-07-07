<?php

namespace Outlandish\PhpCrudApi;

abstract class TablePermissions
{

    /** @var array Default values columns that can be accessed in read operations (read, list) */
    protected const ALL_READ_COLUMNS = [];

    /** @var array Default values columns that can be accessed in write operations (create, update, increment, delete) */
    protected const ALL_WRITE_COLUMNS = [];

    /** @var array Columns for which the values can be output in read operations from this table */
    protected const READ_COLUMNS = [];

    /** @var array Columns for which the values can be output in list operations on this table */
    protected const LIST_COLUMNS = [];

    /** @var array Columns for which the values can be updated to by update operations on this table */
    protected const UPDATE_COLUMNS = [];

    /** @var array Columns that can be read from this table */
    protected const INCREMENT_COLUMNS = [];

    /** @var array Columns that can be written to in a create operation */
    protected const CREATE_COLUMNS = [];

    /** @var bool Whether to allow delete operations on this table */
    protected const ALLOW_DELETE = false;


    /**
     * The name of this table in the database
     * @return string
     */
    public abstract static function getTableName(): string;

    /**
     * Columns from this table that can be accessed by `read` operations
     * @return array
     */
    public static function getReadColumns(): array
    {
        return static::READ_COLUMNS ?: static::ALL_READ_COLUMNS;
    }


    /**
     * Columns from this table that can be accessed in `list` operations
     * @return array
     */
    public static function getListColumns(): array
    {
        return static::LIST_COLUMNS ?: static::ALL_READ_COLUMNS;
    }

    /**
     * Columns from this table that can be written to in `create` operations
     * @return array
     */
    public static function getCreateColumns(): array
    {
        return static::CREATE_COLUMNS ?: static::ALL_WRITE_COLUMNS;
    }

    /**
     * Columns from this table that can be written to in `update` operations
     * @return array
     */
    public static function getUpdateColumns(): array
    {
        return static::UPDATE_COLUMNS ?: static::ALL_WRITE_COLUMNS;
    }

    /**
     * Whether `delete` operations are allowed on records in this table
     * @return bool
     */
    public static function allowDelete(): bool
    {
        return static::ALLOW_DELETE;
    }

    /**
     * Columns from this table that can be incremented with `incremented` operations
     * @return array
     */
    public static function getIncrementColumns(): array
    {
        return static::INCREMENT_COLUMNS ?: static::ALL_WRITE_COLUMNS;
    }


    /**
     * Output the permissions from this table as an array in the format of `['operation'] => ['allowedColumn1', 'allowedColumn2']`
     * @return array[]|bool[]
     */
    final public static function toArray(): array
    {
        return [
            "read" => static::getReadColumns(),
            "list" => static::getListColumns(),
            "create" => static::getCreateColumns(),
            "update" => static::getUpdateColumns(),
            "increment" => static::getIncrementColumns(),
            "delete" => static::allowDelete(),
        ];
    }

}