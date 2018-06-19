<?php
/**
 * Created by PhpStorm.
 * User: mcasiro
 * Date: 2018-06-12
 * Time: 14:09
 */

define('SOURCE_DIR', '/src/');
function autoLoader($class)
{
    $classDirectory = str_replace('\\', '/', $class);
    $classPath = __DIR__ . SOURCE_DIR . mb_substr($classDirectory, mb_strpos($classDirectory, '/') + 1) . '.php';

    require $classPath;
}

spl_autoload_register('autoLoader');
