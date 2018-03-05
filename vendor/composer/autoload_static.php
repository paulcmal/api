<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3e3ce37d1bbd95ee518170a944ffea90
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'cmal\\Api\\' => 9,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'cmal\\Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3e3ce37d1bbd95ee518170a944ffea90::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3e3ce37d1bbd95ee518170a944ffea90::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}