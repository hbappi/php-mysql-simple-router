<?php


return function ($router) {

    $router->use("/api", ['CookiesMiddleware']);

    $router->post("/api/category/paginate", ['CategoryController', 'paginate']);
    $router->post("/api/category/upsert", ['CategoryController', 'upsert']);
    // $router->post("/api/category/paginate", ['CategoryController', 'paginate']);




};
