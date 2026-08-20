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
        self::assertStringContainsString('>Open</f:be.link>', $template);
        self::assertStringNotContainsString('Independent TYPO3 products', $template);
        self::assertStringNotContainsString('vm-product__mark', $template);
        self::assertStringNotContainsString('Open product', $template);
        self::assertStringNotContainsString('composer require {product.composerPackage}', $template);
    }

    public function testDashboardUsesTheViewMendVmBrandMark(): void
    {
        $icon = file_get_contents(dirname(__DIR__, 2) . '/Resources/Public/Icons/viewmend-product-mark.svg');

        self::assertIsString($icon);
        self::assertStringContainsString('viewBox="0 0 21 21"', $icon);
        self::assertStringContainsString('id="viewmend-vm-v"', $icon);
        self::assertStringContainsString('id="viewmend-vm-m"', $icon);
        self::assertStringContainsString('fill="url(#viewmend-vm-v)"', $icon);
        self::assertStringContainsString('fill="url(#viewmend-vm-m)"', $icon);
        self::assertStringNotContainsString('currentColor', $icon);
    }

    public function testInitialPublicCatalogContainsOnlyAutoReplies(): void
    {
        $catalog = file_get_contents(dirname(__DIR__, 2) . '/Classes/Product/ProductCatalog.php');

        self::assertIsString($catalog);
        self::assertStringContainsString("'viewmend/typo3-auto-replies'", $catalog);
        self::assertSame(1, substr_count($catalog, 'new ProductDefinition('));
    }
}
