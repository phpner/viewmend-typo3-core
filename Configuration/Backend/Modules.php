<?php

declare(strict_types=1);

use ViewMend\Typo3Core\Backend\Controller\ProductsController;

return [
    'viewmend' => [
        'parent' => 'web',
        'position' => ['after' => 'web_list'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/viewmend',
        'iconIdentifier' => 'viewmend-product-mark',
        'labels' => [
            'title' => 'ViewMend',
            'description' => 'Installed ViewMend products and compatible additions.',
            'shortDescription' => 'Website operations',
        ],
        'inheritNavigationComponentFromMainModule' => false,
        'appearance' => [
            'dependsOnSubmodules' => true,
        ],
    ],
    'viewmend_products' => [
        'parent' => 'viewmend',
        'position' => ['before' => '*'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/viewmend/products',
        'iconIdentifier' => 'viewmend-product-mark',
        'labels' => [
            'title' => 'Products',
            'description' => 'Installed and available ViewMend products.',
            'shortDescription' => 'Product catalog',
        ],
        'extensionName' => 'ViewMendCore',
        'inheritNavigationComponentFromMainModule' => false,
        'controllerActions' => [
            ProductsController::class => ['index'],
        ],
    ],
];
