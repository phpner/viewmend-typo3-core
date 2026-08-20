<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

declare(strict_types=1);

use ViewMend\Typo3Core\Backend\Controller\DashboardController;

return [
    'viewmend' => [
        'position' => ['after' => 'site'],
        'workspaces' => 'live',
        'iconIdentifier' => 'viewmend-product-mark',
        'labels' => [
            'title' => 'ViewMend',
            'description' => 'Independent ViewMend products for TYPO3.',
            'shortDescription' => 'Website operations',
        ],
    ],
    'viewmend_dashboard' => [
        'parent' => 'viewmend',
        'position' => ['before' => '*'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/viewmend/dashboard',
        'iconIdentifier' => 'viewmend-product-mark',
        'labels' => [
            'title' => 'Dashboard',
            'description' => 'Installed and available ViewMend products.',
            'shortDescription' => 'Product catalog',
        ],
        'extensionName' => 'ViewMendCore',
        'inheritNavigationComponentFromMainModule' => false,
        'controllerActions' => [
            DashboardController::class => ['index'],
        ],
    ],
];
