<?php


return function ($router) {

    $router->use("/", ['TestMiddleware', 'CorsMiddleware']);

    $router->use("/api", ['CookiesMiddleware']);

    $router->post("/api/category/paginate", ['CategoryController', 'paginate']);
    $router->post("/api/category/upsert", ['CategoryController', 'upsert']);
    $router->post("/api/category/delete", ['CategoryController', 'delete']);

    $router->post("/api/app/paginate", ['AppController', 'paginateApp']);
    $router->post("/api/app/upsert", ['AppController', 'upsertApp']);
    $router->post("/api/app/delete", ['AppController', 'deleteApp']);
    // $router->post("/api/category/paginate", ['CategoryController', 'paginate']);




};
