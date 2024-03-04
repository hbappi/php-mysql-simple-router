<?php
class CategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function paginate()
    {

        $payload = parent::getJsonBody();

        // return $payload;


        return parent::dbExec('__category_paginate', $payload); // returns ['ret_data', 'error']
    }
    public function upsert()
    {

        $payload = parent::getJsonBody();

        $categories = $payload['categories'] ?? [];

        foreach ($categories as $category) {
            $res = parent::dbExec('__category_upsert', $category);

            if ($res['error'] ?? false) {
                return $res;
            }
        }

        return [
            'ret_data' => 'success'
        ];
    }
    public function delete()
    {
    }
}
