<?php

use flight\Engine;

/** @var Engine $app */

// Set the default timezone
date_default_timezone_set('America/New_York');

// Set the error reporting level
error_reporting(E_ALL);

// Set the default character encoding
if(function_exists('mb_internal_encoding') === true) {
	mb_internal_encoding('UTF-8');
}

// Set the default locale
if(function_exists('setlocale') === true) {
	setlocale(LC_ALL, 'en_US.UTF-8');
}

/* 
 * Set some flight variables
 */
$app->set('flight.base_url', '/'); // if this is in a subdirectory, you'll need to change this
$app->set('flight.case_sensitive', false); // if you want case sensitive routes, set this to true
$app->set('flight.log_errors', true); // if you want to log errors, set this to true
$app->set('flight.handle_errors', true); // if you want flight to handle errors, set this to true
$app->set('flight.views.path', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views'); // set the path to your view/template/ui files
$app->set('flight.views.extension', '.php'); // set the file extension for your view/template/ui files
$app->set('flight.content_length', true); // if flight should send a content length header

// This makes sure that any new classes you have in you project are autoloaded
$app->path(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
// To autoload a class, let's say it's the app/controllers/ExampleController.php class
// 		Make sure to have the class namespaced to app\controllers
//		Make sure that your class has NO underscores in it. Underscores are not allowed in class names.

/* 
 * This is where you will store database credentials, api credentials
 * and other sensitive information. This file will not be tracked by git
 * as you shouldn't be pushing sensitive information to a public or private
 * repository.
 * 
 * What you store here is totally up to you.
 */
return [
	'database' => [
		'host' => 'localhost',
		'dbname' => 'dbname',
		'user' => 'user',
		'password' => 'password'
	],
	'google_oauth' => [
		'client_id' => 'client_id',
		'client_secret' => 'client_secret',
		'redirect_uri' => 'redirect_uri'
	],
];