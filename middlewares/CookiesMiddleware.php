<?php

class CookiesMiddleware extends Middleware
{
    public function handle($next)
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
        echo 'coockies middleware called';

        return $next();
    }
}
