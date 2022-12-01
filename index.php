<?php

use App\App;

define("APP_URL", sprintf('http://%s/advent_of_code_2022/', $_SERVER['HTTP_HOST']));

require __DIR__ . '/vendor/autoload.php';
require "src\App.php";

$app = new App();
$app->run();