<?php

namespace Outlandish\PhpCrudApi;

class TablePermissions
{
    const TABLE_NAME = "";
    
    private const read_columns = [];
    private const list_columns = [];
    private const update_columns = [];
    private const delete_columns = [];
    private const increment_columns = [];

    /**
     * @return array
     */
    public static function getReadColumns(): array
    {
        return self::read_columns;
    }


    /**
     * @return array
     */
    public static function getListColumns(): array
    {
        return self::list_columns;
    }

    /**
     * @return array
     */
    public static function getUpdateColumns(): array
    {
        return self::update_columns;
    }

    /**
     * @return array
     */
    public static function getDeleteColumns(): array
    {
        return self::delete_columns;
    }

    /**
     * @return array
     */
    public static function getIncrementColumns(): array
    {
        return self::increment_columns;
    }


}

/* and then in your app you'd have */


class PetsPermissions extends TablePermissions
{
    private const read_columns = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];
    private const write_columns = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];

    public static function getReadColumns(): array
    {
        return self::read_columns;
    }


    /**
     * @return array
     */
    public static function getListColumns(): array
    {
        return self::read_columns;
    }

    /**
     * @return array
     */
    public static function getUpdateColumns(): array
    {
        return self::update_columns;
    }

    /**
     * @return array
     */
    public static function getDeleteColumns(): array
    {
        return self::write_columns;
    }

    /**
     * @return array
     */
    public static function getIncrementColumns(): array
    {
        $increment = self::read_columns;
        $increment[] = 'favourites';
        return $increment;
    }
    
}


class UserPermissions extends TablePermissions
{
    private const read_columns = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];
    public static function getListColumns(): array
    {
        return self::read_columns;
    }

}