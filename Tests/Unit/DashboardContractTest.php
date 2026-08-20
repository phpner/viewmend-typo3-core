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
        self::assertStringContainsString('id="vm-products-title">Products</h1>', $template);
        self::assertStringContainsString('data-product-filter="installed"', $template);
        self::assertStringContainsString('data-product-filter="available"', $template);
        self::assertStringContainsString('data-install-dialog', $template);
        self::assertStringContainsString('data-open-install', $template);
        self::assertStringContainsString('product.accessible', $template);
        self::assertStringContainsString('product.installCommand', $template);
        self::assertStringContainsString('Open {product.title}', $template);
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

    public function testOfficialCatalogSeparatesInstallableAndPlannedProducts(): void
    {
        $catalog = file_get_contents(dirname(__DIR__, 2) . '/Classes/Product/ProductCatalog.php');

        self::assertIsString($catalog);
        self::assertStringContainsString("'viewmend/typo3-auto-replies'", $catalog);
        self::assertStringContainsString("'viewmend/typo3-site-tracker'", $catalog);
        self::assertStringContainsString("'viewmend/typo3-inboxmend'", $catalog);
        self::assertStringContainsString('installable: false', $catalog);
        self::assertSame(3, substr_count($catalog, 'new ProductDefinition('));
    }

    public function testDashboardUsesDedicatedProductIcons(): void
    {
        $icons = require dirname(__DIR__, 2) . '/Configuration/Icons.php';

        self::assertArrayHasKey('viewmend-product-auto-replies', $icons);
        self::assertArrayHasKey('viewmend-product-site-tracker', $icons);
        self::assertArrayHasKey('viewmend-product-inboxmend', $icons);
    }
}
