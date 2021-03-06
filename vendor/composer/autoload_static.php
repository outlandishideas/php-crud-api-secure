<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit28d88a550ef6ac81987c6935a659c527
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tqdev\\PhpCrudApi\\' => 17,
        ),
        'P' => 
        array (
            'Psr\\Http\\Server\\' => 16,
            'Psr\\Http\\Message\\' => 17,
        ),
        'N' => 
        array (
            'Nyholm\\Psr7\\' => 12,
            'Nyholm\\Psr7Server\\' => 18,
        ),
        'H' => 
        array (
            'Http\\Message\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tqdev\\PhpCrudApi\\' => 
        array (
            0 => __DIR__ . '/..' . '/mevdschee/php-crud-api/src/Tqdev/PhpCrudApi',
            1 => __DIR__ . '/..' . '/mevdschee/php-crud-api/src/Tqdev/PhpCrudApi',
        ),
        'Psr\\Http\\Server\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-server-handler/src',
            1 => __DIR__ . '/..' . '/psr/http-server-middleware/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Nyholm\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/nyholm/psr7/src',
        ),
        'Nyholm\\Psr7Server\\' => 
        array (
            0 => __DIR__ . '/..' . '/nyholm/psr7-server/src',
        ),
        'Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-http/message-factory/src',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/src',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit28d88a550ef6ac81987c6935a659c527::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit28d88a550ef6ac81987c6935a659c527::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInit28d88a550ef6ac81987c6935a659c527::$fallbackDirsPsr4;
            $loader->classMap = ComposerStaticInit28d88a550ef6ac81987c6935a659c527::$classMap;

        }, null, ClassLoader::class);
    }
}
