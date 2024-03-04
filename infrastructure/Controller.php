<?php

require_once __DIR__ . "/Db.php";

class Controller
{
    public function __construct()
    {
    }

    public function dbExec($fn, $payload = array())
    {
        // call naz.route($fn, $payload ,'{}', @out); select @out;
        return Db::getInstance()->execute($fn, $payload);
    }
}
