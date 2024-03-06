<?php
class AppController extends Controller
{

    public function paginateApp(\Request $req, \Response $res)
    {

        // $payload = parent::getJsonBody();
        $payload = $req->getJsonBody();

        // return $payload;

        parent::dbExec('__app_paginate', $payload, $res);


        // $result =  parent::dbExec('__category_paginate', $payload); // returns ['ret_data', 'error']
        // $res->setBody($result)->end();
    }
    public function upsertApp(\Request $req, \Response $res)
    {

        $payload = $req->getJsonBody();

        $categories = $payload['apps'] ?? [];

        foreach ($categories as $category) {
            $result = parent::dbExec('__app_upsert', $category);

            if ($result['error'] ?? false) {
                return $res->json($result)->end();
            }
        }

        $res->json([
            'ret_data' => 'success'
        ])->end();
    }
    public function deleteApp($req, $res)
    {
        $payload = $req->getJsonBody();

        $result = parent::dbExec('__app_delete', $payload);
        
        if ($result['error'] ?? false) {
            return $res->json($result)->end();
        }

        $res->json([
            'ret_data' => 'success'
        ])->end();
    }
}
