<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'ViewMend Core',
    'description' => 'Shared ViewMend menu and product registry for independently installed TYPO3 extensions.',
    'category' => 'module',
    'author' => 'ViewMend',
    'author_email' => 'support@viewmend.com',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
