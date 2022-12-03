<?php

use App\App;

define("APP_URL", sprintf('http://%s/advent_of_code_2022/', $_SERVER['HTTP_HOST']));
define("APP_PUZZLES_PATH", 'public/puzzles/');

require __DIR__ . '/vendor/autoload.php';
require "src\App.php";

$app = new App();
$app->run();