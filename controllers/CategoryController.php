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


        return parent::dbExec('__category_paginate', $payload);
    }
    public function upsert()
    {

        $payload = array();

        $payload['a'] = 'thisa';



        parent::dbExec('__category_upsert', $payload);
    }
    public function delete()
    {
    }
}
