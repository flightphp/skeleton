<?php

declare(strict_types=1);

namespace App\Controller;

use App\Utils\Config;
use flight\Engine;

/**
 * Welcome page controller — canonical controller example for the skeleton.
 *
 * No Flight:: facade. No $_ENV. Inject what you need.
 */
class HomeController
{
    /** @var Engine<object> */
    private $app;

    /** @var Config */
    private $config;

    /**
     * @param Engine<object> $app
     */
    public function __construct(Engine $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function index(): void
    {
        $this->app->render('welcome', [
            'message' => 'You are gonna do great things!',
            'env' => $this->config->env(),
            'debug' => $this->config->isDebug(),
        ]);
    }
}
