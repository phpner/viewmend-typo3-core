<?php

declare(strict_types=1);

namespace ViewMend\Typo3Core\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class DashboardContractTest extends TestCase
{
    public function testViewMendIsATopLevelGroupWithDashboardFirst(): void
    {
        $modules = require dirname(__DIR__, 2) . '/Configuration/Backend/Modules.php';

        self::assertArrayHasKey('viewmend', $modules);
        self::assertArrayNotHasKey('parent', $modules['viewmend']);
        self::assertArrayNotHasKey('path', $modules['viewmend']);
        self::assertArrayHasKey('viewmend_dashboard', $modules);
        self::assertSame('viewmend', $modules['viewmend_dashboard']['parent']);
        self::assertSame(['before' => '*'], $modules['viewmend_dashboard']['position']);
        self::assertSame('/module/viewmend/dashboard', $modules['viewmend_dashboard']['path']);
    }

    public function testDashboardShowsAvailableProductsWithoutDeadNavigationLinks(): void
    {
        $template = file_get_contents(dirname(__DIR__, 2) . '/Resources/Private/Templates/Dashboard/Index.html');

        self::assertIsString($template);
        self::assertStringContainsString('ViewMend Dashboard', $template);
        self::assertStringContainsString('product.accessible', $template);
        self::assertStringContainsString('product.installCommand', $template);
        self::assertStringNotContainsString('composer require {product.composerPackage}', $template);
    }
}
