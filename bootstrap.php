<?php
    require_once 'vendor/trails/trails.php';
    require_once 'app/controllers/studip_controller.php';
    require_once 'classes/GlossarController.php';

    require_once 'classes/Plain_ORM.php';
    require_once 'models/GlossarCategory.php';
    require_once 'models/GlossarContext.php';
    require_once 'models/GlossarEntry.php';
    require_once 'models/GlossarList.php';

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
