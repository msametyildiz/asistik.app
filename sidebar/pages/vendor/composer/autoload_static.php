<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1154e47cfe8c24bacb203becf1ee69cf
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Samet\\Asistik\\' => 14,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Samet\\Asistik\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1154e47cfe8c24bacb203becf1ee69cf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1154e47cfe8c24bacb203becf1ee69cf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1154e47cfe8c24bacb203becf1ee69cf::$classMap;

        }, null, ClassLoader::class);
    }
}
