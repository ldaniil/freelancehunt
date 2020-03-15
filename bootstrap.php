<?php

require_once './vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load('.env');

$capsule = new Capsule();
$capsule->addConnection([
	'driver'    => $_ENV['DB_DRIVER'],
	'host'      => $_ENV['DB_HOST'],
	'database'  => $_ENV['DB_NAME'],
	'username'  => $_ENV['DB_USER'],
	'password'  => $_ENV['DB_PASS'],
	'charset'   => $_ENV['DB_CHARSET'],
	'collation' => $_ENV['DB_COLLATION']
]);
$capsule->setAsGlobal();

