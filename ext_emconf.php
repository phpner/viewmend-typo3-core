<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'ViewMend Core',
    'description' => 'Shared ViewMend menu and product registry for independently installed TYPO3 extensions.',
    'category' => 'module',
    'author' => 'ViewMend',
    'author_email' => 'support@viewmend.com',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
