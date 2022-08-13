<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc030ffb4d807e8b79f5a29c894d904d3
{
    public static $prefixLengthsPsr4 = array (
        'y' => 
        array (
            'yidas\\' => 6,
        ),
        'S' => 
        array (
            'Svg\\' => 4,
            'Sabberworm\\CSS\\' => 15,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'F' => 
        array (
            'FontLib\\' => 8,
        ),
        'D' => 
        array (
            'Dompdf\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'yidas\\' => 
        array (
            0 => __DIR__ . '/..' . '/yidas/codeigniter-rest/src',
        ),
        'Svg\\' => 
        array (
            0 => __DIR__ . '/..' . '/phenx/php-svg-lib/src/Svg',
        ),
        'Sabberworm\\CSS\\' => 
        array (
            0 => __DIR__ . '/..' . '/sabberworm/php-css-parser/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'FontLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phenx/php-font-lib/src/FontLib',
        ),
        'Dompdf\\' => 
        array (
            0 => __DIR__ . '/..' . '/dompdf/dompdf/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Dompdf\\Cpdf' => __DIR__ . '/..' . '/dompdf/dompdf/lib/Cpdf.php',
        'HTML5_Data' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Data.php',
        'HTML5_InputStream' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/InputStream.php',
        'HTML5_Parser' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Parser.php',
        'HTML5_Tokenizer' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Tokenizer.php',
        'HTML5_TreeBuilder' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/TreeBuilder.php',
        'SB_Controller' => __DIR__ . '/../../..' . '/application/core/SB_Controller.php',
        'SB_Exception' => __DIR__ . '/../../..' . '/application/core/SB_Exception.php',
        'SB_Input' => __DIR__ . '/../../..' . '/application/core/SB_Input.php',
        'SB_Loader' => __DIR__ . '/../../..' . '/application/core/SB_Loader.php',
        'SB_Model' => __DIR__ . '/../../..' . '/application/core/SB_Model.php',
        'SB_Rest' => __DIR__ . '/../../..' . '/application/core/SB_Rest.php',
        'SB_Router' => __DIR__ . '/../../..' . '/application/core/SB_Router.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc030ffb4d807e8b79f5a29c894d904d3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc030ffb4d807e8b79f5a29c894d904d3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc030ffb4d807e8b79f5a29c894d904d3::$classMap;

        }, null, ClassLoader::class);
    }
}
