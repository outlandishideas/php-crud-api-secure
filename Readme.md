# Secure PHP-CRUD-API

A wrapper around [mevdschee/php-crud-api](https://github.com/mevdschee/php-crud-api) which makes it secure by default,
by ensuring that the `authorization` middleware is enabled and has handlers for tables and columns.

## Usage

This library is used in exactly the same way as [mevdschee/php-crud-api](https://github.com/mevdschee/php-crud-api)
except that it will throw a `InvalidArgumentException` if the `authorization`, `authorization.tableHandler` and
`authorization.tableHandler` middleware properties are not set in the API contructor.

### Using custom `tableHandler` and `columnHandler` functions:

Basic use case e.g. for Slim/Laravel app:

```php

use Slim\App;
use Outlandish\PhpCrudApi\SecureConfig;
require 'vendor/autoload.php';

return function (App $app) {
    $app->get('/api[/{params:.*}]', function (
            Request $request,
            Response $response,
            array $args
        ) {
            $config = new SecureConfig([
                'middlewares' => 'pageLimits,authorization',
                'pageLimits.records' => 2,
                'authorization.tableHandler' => function ($operation, $tableName)  {
                    return $tableName != 'users'; //prevent CRUD api from performing any actions on the users table
                },
                'authorization.columnHandler' =>
                    function ($operation, $tableName, $columnName) {
                        if($tableName == 'participants'){
                            return $columnName != 'last_ip_address';
                        }
                    },
            ]);
            $api = new Api($config);
            $response = $api->handle($request);
            return $response;
        }
    );
};
```

### Using table_column mapping helper

The SecureConfig class can be passed an array of TablePermissions sub-classes to make it easier to explicitly
define which columns from which tables can be operated on:

```php

use Slim\App;
use Outlandish\PhpCrudApi\SecureConfig;
use Tqdev\PhpCrudApi\Api;
use Outlandish\PhpCrudApi\TablePermissions;

require 'vendor/autoload.php';

return function (App $app) {
    $app->get('/api[/{params:.*}]', function (
            Request $request,
            Response $response,
            array $args
        ) {
            class UsersTablePermissions extends TablePermissions {

                protected const ALL_READ_COLUMNS = ["id", "display_name"];
        
                public static function getTableName(): string
                {
                    return "users";
                }
        
            }
            class PetsTablePermissions extends TablePermissions {
        
                protected const ALL_READ_COLUMNS = ["id", "name", "favourite_food", "species", "owner"];
                protected const CREATE_COLUMNS = ["name", "favourite_food", "species", "owner"];
        
                public static function getTableName(): string
                {
                    return "pets";
                }
            }
        
            $table_columns_mapping = [
                PetsTablePermissions::class,
                UsersTablePermissions::class
            ];

            
            $config = new SecureConfig([
                'middlewares' => 'pageLimits',
                'pageLimits.records' => 2,
            ], $table_columns_mapping);
            
            $api = new Api($config);
            $response = $api->handle($request);
            return $response;
        }
    );
};
```

The `TablePermissions` sub-classes can set their permissions either by defining the following constants as 
arrays of column names (or boolean for delete) which does not use columns:

* `ALL_READ_COLUMNS` (default for read/list)
* `ALL_WRITE_COLUMNS` (default for create/update/increment/delete)
* `READ_COLUMNS` 
* `LIST_COLUMNS` 
* `CREATE_COLUMNS` 
* `UPDATE_COLUMNS` 
* `INCREMENT_COLUMNS` 
* `ALLOW_DELETE` (boolean) 

We recommend handling authentication in your outer application rather than using the built in middleware e.g. 

```PHP

class PetsTablePermissions extends TablePermissions {
        
    protected const ALL_READ_COLUMNS = ["id", "name", "favourite_food", "species", "owner"];
    protected const CREATE_COLUMNS = ["name", "favourite_food", "species", "owner"];

    public static function getTableName(): string
    {
        return "pets";
    }
}

class PetsTablePermissionsAuthenticatedUser extends TablePermissions {
        
    protected const ALL_READ_COLUMNS = ["id", "name", "favourite_food", "species", "owner"];
    protected const CREATE_COLUMNS = ["name", "favourite_food", "species", "owner"];

    public static function getTableName(): string
    {
        return "pets";
    }
    
    public static function getUpdateColumns(){
        return static::getReadColumns();
    }
}

if (Auth::check()) {
    // The user is logged in...
    $table_columns_mapping = [
        PetsTablePermissionsAuthenticatedUser::class,
    ];
}else{
    //it's an anonymous user
    $table_columns_mapping = [
        PetsTablePermissions::class,
    ];
}



```