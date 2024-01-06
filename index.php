<?php

define("BASE_PATH", __DIR__);
define("CREDENTIAL", BASE_PATH . "/credentials.json");
require BASE_PATH . "/core/Database.php";
$db = new Database();

require BASE_PATH . "/core/Router.php";
require BASE_PATH . "/routes.php";
?>