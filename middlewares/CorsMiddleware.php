<?php

class CorsMiddleware extends Middleware
{
    public function handle(\Request $req, \Response $res, $next)
    {

        // echo 'cors added';

        $res->addHeader('Access-Control-Allow-Methods', '*');
        $res->addHeader('Access-Control-Allow-Origin', '*');
        $res->addHeader('Access-Control-Allow-Headers', '*');
        $res->addHeader('Access-Control-Allow-Credentials', 'true');
        $res->addHeader('Access-Control-Max-Age', '86400');


        return $next($req, $res);
    }
}
