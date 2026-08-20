<?php

declare(strict_types=1);

namespace ViewMend\Typo3Core\Product;

use TYPO3\CMS\Backend\Module\ModuleProvider;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use ViewMend\Typo3Core\Contract\ProductProviderInterface;

final readonly class ProductCatalog
{
    /** @param iterable<ProductProviderInterface> $providers */
    public function __construct(
        private iterable $providers,
        private ModuleProvider $moduleProvider,
    ) {}

    /** @return list<array<string, bool|int|string>> */
    public function products(BackendUserAuthentication $user): array
    {
        $definitions = $this->officialProducts();
        foreach ($this->providers as $provider) {
            $product = $provider->product();
            $definitions[$product->identifier] = $product;
        }

        uasort(
            $definitions,
            static fn(ProductDefinition $left, ProductDefinition $right): int =>
                [$left->position, $left->title] <=> [$right->position, $right->title]
        );

        $products = [];
        foreach ($definitions as $definition) {
            $installed = ExtensionManagementUtility::isLoaded($definition->extensionKey);
            $accessible = $installed
                && $definition->moduleIdentifier !== ''
                && $this->moduleProvider->getModule($definition->moduleIdentifier, $user) !== null;
            $product = $definition->toArray();
            $product['installed'] = $installed;
            $product['accessible'] = $accessible;
            $product['status'] = $installed ? ($accessible ? 'installed' : 'restricted') : 'available';
            $product['statusLabel'] = $installed ? ($accessible ? 'Installed' : 'Installed · no access') : 'Available';
            $product['installCommand'] = 'composer require ' . $definition->composerPackage;
            $product['mark'] = $this->mark($definition->title);
            $products[] = $product;
        }

        return $products;
    }

    /** @return array<string, ProductDefinition> */
    private function officialProducts(): array
    {
        return [
            'site-tracker' => new ProductDefinition(
                'site-tracker',
                'Site Tracker',
                'Monitor important public pages after TYPO3 content changes and deployments.',
                'viewmend/typo3-site-tracker',
                'viewmend_site_tracker',
                'viewmend_site_tracker',
                'Monitoring',
                20,
            ),
            'inboxmend' => new ProductDefinition(
                'inboxmend',
                'InboxMend',
                'Store, triage, assign, and audit form submissions locally.',
                'viewmend/typo3-inboxmend',
                'inboxmend',
                'inboxmend',
                'Submissions',
                30,
            ),
            'auto-replies' => new ProductDefinition(
                'auto-replies',
                'Auto-replies',
                'Build conditional visitor acknowledgements and deliver them through a durable local queue.',
                'viewmend/typo3-auto-replies',
                'viewmend_auto_replies',
                'viewmend_auto_replies',
                'Messaging',
                40,
            ),
        ];
    }

    private function mark(string $title): string
    {
        $words = preg_split('/[^A-Za-z0-9]+/', trim($title), -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($words) || $words === []) {
            return 'VM';
        }
        if (count($words) > 1) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($words[0], 0, 2));
    }
}
