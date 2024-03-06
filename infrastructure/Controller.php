<?php


class Controller
{
    public function __construct()
    {
    }

    public function dbExec($fn, $payload = array(), \Response $res = null)
    {
        // call naz.route($fn, $payload ,'{}', @out); select @out;

        $result =  Db::getInstance()->execute($fn, $payload);

        if (isset($res)) {
            return $res->json($result)->send();
        }
        return $result;
    }
    // public function getJsonBody()
    // {
    //     $jsonData = array();

    //     $entityBody = file_get_contents('php://input');

    //     if (!empty($entityBody)) {
    //         $jsonData = json_decode($entityBody, true);
    //     }

    //     return $jsonData;
    // }
}
