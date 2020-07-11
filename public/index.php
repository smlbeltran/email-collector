<?php

use EmailCollector\ApplicationFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

define("APP_PATH", realpath(dirname(__FILE__)));
ini_set('xdebug.overload_var_dump', 0);

$factory = new ApplicationFactory();
$factory->make();
