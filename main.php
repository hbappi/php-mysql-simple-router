<?php

define('ABSPATH', __DIR__);

require_once ABSPATH . "/infrastructure/RequireAll.php";


Router::getInstance()->dispatch();
