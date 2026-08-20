<?php

declare(strict_types=1);

namespace ViewMend\Typo3Core\Product;

final readonly class ProductDefinition
{
    public function __construct(
        public string $identifier,
        public string $title,
        public string $description,
        public string $composerPackage,
        public string $extensionKey,
        public string $moduleIdentifier,
        public string $category,
        public int $position = 100,
    ) {}

    /** @return array<string, int|string> */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'title' => $this->title,
            'description' => $this->description,
            'composerPackage' => $this->composerPackage,
            'extensionKey' => $this->extensionKey,
            'moduleIdentifier' => $this->moduleIdentifier,
            'category' => $this->category,
            'position' => $this->position,
        ];
    }
}
