<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

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
        self::assertSame('viewmend-product-mark', $modules['viewmend']['iconIdentifier']);
        self::assertArrayHasKey('viewmend_dashboard', $modules);
        self::assertSame('viewmend', $modules['viewmend_dashboard']['parent']);
        self::assertSame(['before' => '*'], $modules['viewmend_dashboard']['position']);
        self::assertSame('/module/viewmend/dashboard', $modules['viewmend_dashboard']['path']);
        self::assertSame('viewmend-dashboard', $modules['viewmend_dashboard']['iconIdentifier']);
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
        self::assertStringContainsString("'viewmend/typo3-mailings'", $catalog);
        self::assertStringContainsString("'viewmend/typo3-inboxmend'", $catalog);
        self::assertSame(3, substr_count($catalog, 'installable: false'));
        self::assertSame(4, substr_count($catalog, 'new ProductDefinition('));
    }

    public function testDashboardUsesDedicatedProductIcons(): void
    {
        $icons = require dirname(__DIR__, 2) . '/Configuration/Icons.php';

        self::assertArrayHasKey('viewmend-dashboard', $icons);
        self::assertArrayHasKey('viewmend-product-auto-replies', $icons);
        self::assertArrayHasKey('viewmend-product-site-tracker', $icons);
        self::assertArrayHasKey('viewmend-product-mailings', $icons);
        self::assertArrayHasKey('viewmend-product-inboxmend', $icons);
    }

    public function testDashboardIconUsesTheProductNavigationStrokeStyle(): void
    {
        $icon = file_get_contents(dirname(__DIR__, 2) . '/Resources/Public/Icons/dashboard.svg');

        self::assertIsString($icon);
        self::assertStringContainsString('<title id="viewmend-dashboard-title">Dashboard</title>', $icon);
        self::assertSame(4, substr_count($icon, '<rect'));
        self::assertStringContainsString('stroke-width="3.5"', $icon);
        self::assertStringNotContainsString('<linearGradient', $icon);
    }

    public function testBackendShellKeepsProductNavigationCompact(): void
    {
        $configuration = file_get_contents(dirname(__DIR__, 2) . '/ext_localconf.php');
        $stylesheet = file_get_contents(dirname(__DIR__, 2) . '/Resources/Public/Css/backend-shell.css');
        $listener = file_get_contents(dirname(__DIR__, 2) . '/Classes/Backend/EventListener/LoadBackendShellJavaScript.php');
        $menu = file_get_contents(dirname(__DIR__, 2) . '/Resources/Public/JavaScript/module-menu.js');

        self::assertIsString($configuration);
        self::assertIsString($stylesheet);
        self::assertIsString($listener);
        self::assertIsString($menu);
        self::assertStringContainsString('PKG:viewmend/typo3-core:Resources/Public/Css/backend-shell.css', $configuration);
        self::assertStringContainsString('calc(var(--modulemenu-icon-size) * .8)', $stylesheet);
        self::assertStringContainsString("JavaScriptModuleInstruction::create('@viewmend/core/module-menu.js')", $listener);
        self::assertStringContainsString("identifier: 'viewmend_email'", $menu);
        self::assertStringContainsString("title: 'Email'", $menu);
        self::assertStringContainsString("modules: ['viewmend_auto_replies', 'viewmend_mailings', 'viewmend_inboxmend']", $menu);
        self::assertStringContainsString("moduleItem.dataset.modulemenuLevel = '3'", $menu);
        self::assertStringContainsString("button.dataset.modulemenuCollapsible = 'true'", $menu);
        self::assertStringContainsString('parentList.append(item)', $menu);
        self::assertStringNotContainsString('content:', $stylesheet);
    }
}
