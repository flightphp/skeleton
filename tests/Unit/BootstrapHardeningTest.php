<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Guards the security switches we expect in bootstrap for new projects.
 * Runtime View::$restrictToPath requires flightphp/core with that property.
 */
class BootstrapHardeningTest extends TestCase
{
    public function testBootstrapEnablesViewPathRestrictionAndDisablesMethodOverride(): void
    {
        $bootstrap = file_get_contents(dirname(__DIR__, 2) . '/app/config/bootstrap.php');
        $this->assertNotFalse($bootstrap);
        $this->assertStringContainsString("set('flight.allow_method_override', false)", $bootstrap);
        $this->assertStringContainsString("property_exists($view, 'restrictToPath')", $bootstrap);
        $this->assertStringContainsString('restrictToPath = true', $bootstrap);
    }
}
