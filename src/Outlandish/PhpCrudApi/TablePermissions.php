<?php

namespace Outlandish\PhpCrudApi;

class TablePermissions
{
    const READ_COLUMNS = [];
    const WRITE_COLUMNS = [];
    const LIST_COLUMNS = [];
    const UPDATE_COLUMNS = [];
    const DELETE_COLUMNS = [];
    const INCREMENT_COLUMNS = [];


}

/* and then in your app you'd have */

class PetsPermissions extends TablePermissions
{
    public const READ_COLUMNS = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];

    public const WRITE_COLUMNS = [
        'id',
        'owner',
        'favourite_food',
        'species',
    ];
}

class UserPermissions extends TablePermissions
{
    public const READ_COLUMNS = [
        'id',
        'display_name',
    ];

}