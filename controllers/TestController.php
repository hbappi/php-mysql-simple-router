<?php

require_once(dirname(__FILE__) . "/../infrastructure/Controller.php");


class TestController extends Controller
{
    public function method1()
    {
        return "method 1 called";
    }
}
