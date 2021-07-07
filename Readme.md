# Secure PHP-CRUD-API

A wrapper around [mevdschee/php-crud-api](https://github.com/mevdschee/php-crud-api) which makes it secure by default,
by ensuring that the `authorization` middleware is enabled and has handlers for tables and columns.

## Usage

This library is used in exactly the same way as [mevdschee/php-crud-api](https://github.com/mevdschee/php-crud-api)
except that it will throw a `InvalidArgumentException` if the `authorization`, `authorization.tableHandler` and
`authorization.tableHandler` middleware properties are not set in the API constructor.

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
                        return false;
                    },
            ]);
            $api = new Api($config);
            $response = $api->handle($request);
            return $response;
        }
    );
};
```

### Using TablePermissions helper

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
            class UsersTablePermissions extends TablePermissions
            {
                public function __construct()
                {
                    parent::__construct('users');
                    $this->allReadColumns = ["id", "display_name"];
                }
        
            }

            class PetsTablePermissions extends TablePermissions
            {
                public function __construct()
                {
                    parent::__construct('pets');
                    $this->allReadColumns = ["id", "name", "favourite_food", "species", "owner"];
                    $this->createColumns = ["name", "favourite_food", "species", "owner"];
                }
            }
        
            $tablePermissions = [
                PetsTablePermissions::getInstance(),
                UsersTablePermissions::getInstance()
            ];

            
            $config = new SecureConfig([
                'middlewares' => 'pageLimits',
                'pageLimits.records' => 2,
            ], $tablePermissions);
            
            $api = new Api($config);
            $response = $api->handle($request);
            return $response;
        }
    );
};
```

The `TablePermissions` sub-classes can set their column permissions with the `xyzColumns` properties below (as 
arrays of column names), and whether they can be deleted:

* `allReadColumns` (default for read/list)
* `allWriteColumns` (default for create/update/increment/delete)
* `readColumns` 
* `listColumns` 
* `createColumns` 
* `updateColumns` 
* `incrementColumns` 
* `canDelete` (boolean) 

We recommend handling authentication in your outer application rather than using the built-in middleware e.g. 

```PHP

class PetsTablePermissions extends TablePermissions
{
    public function __construct()
    {
        parent::__construct('pets');
        $this->allReadColumns = ["id", "name", "favourite_food", "species", "owner"];
        $this->createColumns = ["name", "favourite_food", "species", "owner"];
    }
}

class PetsTablePermissionsAuthenticatedUser extends PetsTablePermissions
{
    public function getUpdateColumns(){
        return $this->getReadColumns();
    }
}

if (Auth::check()) {
    // The user is logged in...
    $tablePermissions = [
        PetsTablePermissionsAuthenticatedUser::getInstance(),
    ];
}else{
    //it's an anonymous user
    $tablePermissions = [
        PetsTablePermissions::getInstance(),
    ];
}



```
