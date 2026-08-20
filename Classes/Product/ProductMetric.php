<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

declare(strict_types=1);

namespace ViewMend\Typo3Core\Product;

final readonly class ProductMetric
{
    public function __construct(
        public string $label,
        public string $value,
    ) {}

    /** @return array{label:string,value:string} */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->value,
        ];
    }
}
