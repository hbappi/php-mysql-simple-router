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
        $jsonData = array();

        $entityBody = file_get_contents('php://input');

        if (!empty($entityBody)) {
            $jsonData = json_decode($entityBody, true);
        }

        return $jsonData;
    }
}
