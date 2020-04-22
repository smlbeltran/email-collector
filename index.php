<?php

use EmailCollector\ApplicationFactory;

require __DIR__ . '/vendor/autoload.php';

session_start();

define("APP_PATH", realpath(dirname(__FILE__)));

$factory = new ApplicationFactory();
$factory->make();
