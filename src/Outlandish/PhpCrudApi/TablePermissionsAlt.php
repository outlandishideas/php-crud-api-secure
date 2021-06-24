<?php

namespace Outlandish\PhpCrudApi;

class TablePermissionsAlt
{
    protected $read_columns = [];
    protected $list_columns = [];
    protected $update_columns = [];
    protected $delete_columns = [];
    protected $increment_columns = [];

    /**
     * @return array
     */
    public function getReadColumns(): array
    {
        return $this->read_columns;
    }


    /**
     * @return array
     */
    public function getListColumns(): array
    {
        return $this->list_columns;
    }

    /**
     * @return array
     */
    public function getUpdateColumns(): array
    {
        return $this->update_columns;
    }

    /**
     * @return array
     */
    public function getDeleteColumns(): array
    {
        return $this->delete_columns;
    }

    /**
     * @return array
     */
    public function getIncrementColumns(): array
    {
        return $this->increment_columns;
    }


}

/* and then in your app you'd have */


class PetsPermissions extends TablePermissionsAlt
{
    protected $read_columns = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];
    protected $write_columns = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];

    public function getReadColumns(): array
    {
        return $this->read_columns;
    }


    /**
     * @return array
     */
    public function getListColumns(): array
    {
        return $this->read_columns;
    }

    /**
     * @return array
     */
    public function getUpdateColumns(): array
    {
        return $this->update_columns;
    }

    /**
     * @return array
     */
    public function getDeleteColumns(): array
    {
        return $this->write_columns;
    }

    /**
     * @return array
     */
    public function getIncrementColumns(): array
    {
        $increment = $this->write_columns;
        $increment[] = 'favourites';
        return $increment;
    }



}
class UserPermissions extends TablePermissions
{
    protected $read_columns = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];
    public function getListColumns(): array
    {
        return $this->read_columns;
    }

}