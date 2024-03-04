<?php



return [
    'GET' => [

        '/api/test' => ['TestController', 'method1'],

    ],
    'POST' => [

        '/test' => ['TestController', 'method1'],
        '/api/category/paginate' => ['CategoryController', 'paginate'],
        '/api/category/delete' => ['CategoryController', 'delete'],
        '/api/category/upsert' => ['CategoryController', 'upsert'],


    ],
];
