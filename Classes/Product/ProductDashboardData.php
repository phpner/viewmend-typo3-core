<?php

/*
 * SPDX-License-Identifier: GPL-2.0-or-later
 */

declare(strict_types=1);

namespace ViewMend\Typo3Core\Product;

final readonly class ProductDashboardData
{
    /**
     * @param list<string> $metadata
     * @param list<ProductMetric> $metrics
     */
    public function __construct(
        public array $metadata = [],
        public array $metrics = [],
    ) {}

    /** @return array{metadata:list<string>,metrics:list<array{label:string,value:string}>} */
    public function toArray(): array
    {
        return [
            'metadata' => $this->metadata,
            'metrics' => array_map(
                static fn(ProductMetric $metric): array => $metric->toArray(),
                $this->metrics,
            ),
        ];
    }
}
