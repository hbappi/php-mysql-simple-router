<?php

class CookiesMiddleware extends Middleware
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
        // echo 'coockies middleware called';

        // $res->json(['error' => 'cookies middleware called', 'message' => 'cookies middleware did\'t work'])->end();

        // $res->json([])->end();

        return $next($req, $res);
    }
}
