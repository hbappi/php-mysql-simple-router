<?php

class TestMiddleware extends Middleware
{
    public function handle(\Request $req, \Response $res, $next)
    {
        // if ($request->cookies->has("")) {
        //     $cookies = $request->cookies->get("");
        //     if (is_array($cookies)) {
        //         foreach ($cookies as $cookie) {
        //             if (strpos($cookie, "") !== false) {
        //                 $cookie = str_replace("", "", $cookie);
        //             }
        //         }
        //     }
        // }
        // echo 'test middleware called';

        // $res->json(['error' => 'test middleware', 'message' => 'test middleware didn\'t passsed'])->end();

        // $res->json(['test' => 'middleware'])->end();


        return $next($req, $res);
    }
}
