<?php

/**
 * Application bootstrap — one robust layout only.
 *
 * Flow:
 *   public/index.php
 *     → vendor/autoload
 *     → load .env → $_ENV
 *     → Flight::app()
 *     → config.php (literals) + Config::mergeEnv
 *     → apply flight.* / timezone / CSP nonce
 *     → services.php (Tracy, SimplePdo, Twig, Session, Dice)
 *     → routes.php
 *     → $app->start()
 */

$ds = DIRECTORY_SEPARATOR;
$projectRoot = dirname(__DIR__, 2);

require $projectRoot . $ds . 'vendor' . $ds . 'autoload.php';

use App\Utils\Config;
use App\Utils\Env;

// Layer 1: .env / real environment (secrets + deploy overrides)
Env::load($projectRoot . $ds . '.env');

$configPath = __DIR__ . $ds . 'config.php';
if (file_exists($configPath) === false) {
    Flight::halt(
        500,
        'Config file not found. Copy app/config/config_sample.php to app/config/config.php'
    );
}

// Prefer instance over static facade for testability
$app = Flight::app();

// Layer 2: literal config.php (Runway config:set / config:get safe)
$fileConfig = require $configPath;

// Layer 3: merge — env wins for mapped keys when set
$merged = Config::mergeEnv($fileConfig, $_ENV);
$config = new Config($merged);

// Side effects from config (not inside the return array)
$timezone = (string) $config->get('app.timezone', 'UTC');
if ($timezone !== '') {
    date_default_timezone_set($timezone);
}

if (function_exists('mb_internal_encoding') === true) {
    mb_internal_encoding('UTF-8');
}

error_reporting(E_ALL);

// Flight core settings from merged Config
$app->set('flight.base_url', $config->baseUrl());
$app->set('flight.case_sensitive', false);
$app->set('flight.log_errors', true);
// Let Tracy handle errors when debug is on
$app->set('flight.handle_errors', $config->isDebug() === false);
$app->set('flight.views.path', __DIR__ . $ds . '..' . $ds . 'views');
$app->set('flight.views.extension', '.twig');
$app->set('flight.content_length', false);

// CSP nonce per request (used by SecurityHeadersMiddleware + Twig globals)
$nonce = bin2hex(random_bytes(16));
$app->set('csp_nonce', $nonce);

// Expose project root for commands/services that need filesystem paths
$app->set('app.project_root', $projectRoot);

// Services: Tracy, DB, Twig, Session, Dice + container handler
require __DIR__ . $ds . 'services.php';

$router = $app->router();
require __DIR__ . $ds . 'routes.php';

$app->start();
