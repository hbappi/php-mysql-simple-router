<?php
class CategoryController extends Controller
{

    public function paginate(\Request $req, \Response $res)
    {

        // $payload = parent::getJsonBody();
        $payload = $req->getJsonBody();

        // return $payload;

        parent::dbExec('__category_paginate', $payload, $res);


        // $result =  parent::dbExec('__category_paginate', $payload); // returns ['ret_data', 'error']
        // $res->setBody($result)->end();
    }
    public function upsert(\Request $req, \Response $res)
    {

        $payload = $req->getJsonBody();

        $categories = $payload['categories'] ?? [];

        foreach ($categories as $category) {
            $result = parent::dbExec('__category_upsert', $category);

            if ($result['error'] ?? false) {
                return $res->json($result)->end();
            }
        }

        $res->json([
            'ret_data' => 'success'
        ])->end();
    }
    public function delete($req, $res)
    {
    }
}
