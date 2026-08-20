<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

declare(strict_types=1);

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!is_file($autoload)) {
    $autoload = dirname(__DIR__, 3) . '/vendor/autoload.php';
}
if (!is_file($autoload)) {
    throw new RuntimeException('Composer autoload.php was not found.');
}
require $autoload;
