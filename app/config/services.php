<?php

use flight\Engine;
use flight\database\PdoWrapper;

/** 
 * @var array $config 
 * @var Engine $app
 */
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';
$app->register('db', PdoWrapper::class, [ $dsn, $config['database']['user'], $config['database']['password'] ]);

// Got google oauth stuff? You could register that here
// $app->register('google_oauth', Google_Client::class, [ $config['google_oauth'] ]);

// Redis? This is where you'd set that up
// $app->register('redis', Redis::class, [ $config['redis']['host'], $config['redis']['port'] ]);