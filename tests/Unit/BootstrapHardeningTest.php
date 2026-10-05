<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Guards the security switches we expect in bootstrap for new projects.
 */
class BootstrapHardeningTest extends TestCase
{
    public function testBootstrapEnablesViewPathRestrictionAndDisablesMethodOverride(): void
    {
        $bootstrap = file_get_contents(dirname(__DIR__, 2) . '/app/config/bootstrap.php');
        $this->assertNotFalse($bootstrap);
        $this->assertStringContainsString("set('flight.allow_method_override', false)", $bootstrap);
        $this->assertStringContainsString("set('flight.views.restrict_to_path', true)", $bootstrap);
    }
}
