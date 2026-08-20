<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'viewmend-product-mark' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:viewmend_core/Resources/Public/Icons/viewmend-product-mark.svg',
    ],
    'viewmend-product-auto-replies' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:viewmend_core/Resources/Public/Icons/product-auto-replies.svg',
    ],
    'viewmend-product-site-tracker' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:viewmend_core/Resources/Public/Icons/product-site-tracker.svg',
    ],
    'viewmend-product-inboxmend' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:viewmend_core/Resources/Public/Icons/product-inboxmend.svg',
    ],
    'viewmend-copy-command' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:viewmend_core/Resources/Public/Icons/copy-command.svg',
    ],
];
