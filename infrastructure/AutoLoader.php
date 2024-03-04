<?php


// spl_autoload_register(function ($className) {
//     $classFile = __DIR__ . '/../controllers/' . $className . '.php';
//     if (file_exists($classFile)) {
//         require_once $classFile;
//     }
// });

function requireFileOnce($classFile)
{

    if (file_exists($classFile)) {
        require_once $classFile;
    }
};
