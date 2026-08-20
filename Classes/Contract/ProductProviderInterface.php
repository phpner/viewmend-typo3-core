<?php

declare(strict_types=1);

namespace ViewMend\Typo3Core\Contract;

use ViewMend\Typo3Core\Product\ProductDefinition;

interface ProductProviderInterface
{
    public function product(): ProductDefinition;
}
