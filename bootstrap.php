<?php
    require_once 'vendor/trails/trails.php';
    require_once 'app/controllers/studip_controller.php';

    StudipAutoloader::addAutoloadPath(__DIR__ . '/classes');
    StudipAutoloader::addAutoloadPath(__DIR__ . '/classes', 'Glossar');
    StudipAutoloader::addAutoloadPath(__DIR__ . '/models', 'Glossar');

    if (!function_exists('array_pluck')) {
        function array_pluck ($array, $key) {
            return array_map(create_function('$item', 'return isset($item["'.$key.'"]) ? $item["'.$key.'"] : null;'), $array);
        }
    }

    if (!function_exists('array_invoke')) {
        function array_invoke($array, $method) {
            $arguments = array_slice(func_get_args(), 2);
            return array_map(create_function('$item', 'return call_user_func_array(array($item, "'.$method.'"), unserialize("'.addslashes(serialize($arguments)).'"));'));
        }
    }
