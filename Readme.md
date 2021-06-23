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

The SecureConfig class can be passed a two dimensional array containing table_names->column_names that can be used to
automatically configure the column and table handlers:

```php

use Slim\App;
use Outlandish\PhpCrudApi\SecureConfig;
use Tqdev\PhpCrudApi\Api;
require 'vendor/autoload.php';

return function (App $app) {
    $app->get('/api[/{params:.*}]', function (
            Request $request,
            Response $response,
            array $args
        ) {
            $allowed_tables_columns = [
                "users" => [
                    'id',
                    'display_name'
                ],
                "pets" => [
                    'id',
                    'name',
                    'favourite_food'
                ]
            ];
            
            $config = new SecureConfig([
                'middlewares' => 'pageLimits',
                'pageLimits.records' => 2,
            ], $allowed_tables_columns);
            
            $api = new Api($config);
            $response = $api->handle($request);
            return $response;
        }
    );
};
```