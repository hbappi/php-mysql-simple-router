<?php


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
    public function getJsonBody()
    {
        $entityBody = file_get_contents('php://input');
        $jsonData = json_decode($entityBody, true);
        return $jsonData;
    }
}
