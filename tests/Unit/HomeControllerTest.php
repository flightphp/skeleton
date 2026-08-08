<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Utils\Config;
use App\Controller\HomeController;
use flight\Engine;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    public function testIndexRendersWelcomeViaEngine(): void
    {
        // render() is a mapped Engine method (via __call), not a real method
        $app = $this->getMockBuilder(Engine::class)
            ->disableOriginalConstructor()
            ->addMethods(['render'])
            ->getMock();

        $app->expects($this->once())
            ->method('render')
            ->with(
                'welcome',
                $this->callback(static function (array $data): bool {
                    return isset($data['message'], $data['env'], $data['debug'])
                        && $data['env'] === 'testing'
                        && $data['debug'] === true;
                })
            );

        $config = new Config([
            'app' => [
                'env' => 'testing',
                'debug' => true,
            ],
        ]);

        $controller = new HomeController($app, $config);
        $controller->index();
    }
}
