<?php


// spl_autoload_register(function ($className) {
//     $classFile = __DIR__ . '/../controllers/' . $className . '.php';
//     if (file_exists($classFile)) {
//         require_once $classFile;
//     }
// });
class AutoLoader
{

    public static function requireFileOnce($classFile)
    {

        if (file_exists($classFile)) {
            require_once $classFile;
        }
    }
}
