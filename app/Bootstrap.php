<?php
session_start();
require dirname(__DIR__) . '/vendor/autoload.php';
define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', dirname(ROOT_PATH) . '/public');
Dotenv\Dotenv::createImmutable(ROOT_PATH)->load();
