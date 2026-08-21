<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'ViewMend Core',
    'description' => 'Manage ViewMend TYPO3 extensions from one shared backend dashboard with unified navigation and product discovery.',
    'category' => 'module',
    'author' => 'ViewMend',
    'author_email' => 'support@viewmend.com',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.0.2',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.5.99',
            'typo3' => '13.4.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
