<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

declare(strict_types=1);

namespace ViewMend\Typo3Core\Contract;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use ViewMend\Typo3Core\Product\ProductDashboardData;

interface ProductDashboardProviderInterface extends ProductProviderInterface
{
    public function dashboardData(BackendUserAuthentication $user): ProductDashboardData;
}
