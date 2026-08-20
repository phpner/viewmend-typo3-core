<?php

declare(strict_types=1);

namespace ViewMend\Typo3Core\Product;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Backend\Module\ModuleProvider;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use ViewMend\Typo3Core\Contract\ProductDashboardProviderInterface;
use ViewMend\Typo3Core\Contract\ProductProviderInterface;

final readonly class ProductCatalog
{
    /** @param iterable<ProductProviderInterface> $providers */
    public function __construct(
        private iterable $providers,
        private ModuleProvider $moduleProvider,
        private LoggerInterface $logger,
    ) {}

    /** @return list<array<string, mixed>> */
    public function products(BackendUserAuthentication $user): array
    {
        $officialDefinitions = $this->officialProducts();
        $definitions = $officialDefinitions;
        $dashboardProviders = [];
        foreach ($this->providers as $provider) {
            $product = $provider->product();
            $definitions[$product->identifier] = $product;
            if ($provider instanceof ProductDashboardProviderInterface) {
                $dashboardProviders[$product->identifier] = $provider;
            }
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
            $officialDefinition = $officialDefinitions[$definition->identifier] ?? null;
            $product['iconIdentifier'] = $definition->iconIdentifier !== ''
                ? $definition->iconIdentifier
                : ($officialDefinition?->iconIdentifier ?: 'viewmend-product-mark');
            $product['installed'] = $installed;
            $product['accessible'] = $accessible;
            $product['filterStatus'] = $installed ? 'installed' : 'available';
            $product['status'] = $installed
                ? ($accessible ? 'installed' : 'restricted')
                : ($definition->installable ? 'available' : 'planned');
            $product['statusLabel'] = $installed
                ? ($accessible ? 'Installed' : 'Installed · no access')
                : ($definition->installable ? 'Available' : 'In development');
            $product['installCommand'] = $definition->installable ? 'composer require ' . $definition->composerPackage : '';
            $version = $installed ? ExtensionManagementUtility::getExtensionVersion($definition->extensionKey) : '';
            $product['version'] = $version;
            $product['versionLabel'] = $version !== '' && preg_match('/^\d/', $version) === 1 ? 'v' . $version : $version;
            $product['dashboard'] = ['metadata' => [], 'metrics' => []];
            if ($accessible && isset($dashboardProviders[$definition->identifier])) {
                try {
                    $product['dashboard'] = $dashboardProviders[$definition->identifier]->dashboardData($user)->toArray();
                } catch (\Throwable $exception) {
                    $this->logger->warning('ViewMend product dashboard data could not be loaded.', [
                        'product' => $definition->identifier,
                        'exception' => $exception::class,
                        'code' => $exception->getCode(),
                    ]);
                }
            }
            $products[] = $product;
        }

        return $products;
    }

    /** @return array<string, ProductDefinition> */
    private function officialProducts(): array
    {
        return [
            'auto-replies' => new ProductDefinition(
                identifier: 'auto-replies',
                title: 'Auto-replies',
                description: 'Send conditional acknowledgements after TYPO3 form submissions through a durable local queue.',
                composerPackage: 'viewmend/typo3-auto-replies',
                extensionKey: 'viewmend_auto_replies',
                moduleIdentifier: 'viewmend_auto_replies',
                category: 'Messaging',
                position: 10,
                iconIdentifier: 'viewmend-product-auto-replies',
            ),
            'site-tracker' => new ProductDefinition(
                identifier: 'site-tracker',
                title: 'Site Tracker',
                description: 'Know what changed after TYPO3 content updates and deployments before visitors report it.',
                composerPackage: 'viewmend/typo3-site-tracker',
                extensionKey: 'viewmend_site_tracker',
                moduleIdentifier: 'tools_viewmendtracker',
                category: 'Monitoring',
                position: 20,
                iconIdentifier: 'viewmend-product-site-tracker',
            ),
            'inboxmend' => new ProductDefinition(
                identifier: 'inboxmend',
                title: 'InboxMend',
                description: 'Keep form submissions searchable, assignable, and auditable inside TYPO3 without sending data elsewhere.',
                composerPackage: 'viewmend/typo3-inboxmend',
                extensionKey: 'viewmend_inboxmend',
                moduleIdentifier: 'viewmend_inboxmend',
                category: 'Submissions',
                position: 30,
                iconIdentifier: 'viewmend-product-inboxmend',
                installable: false,
            ),
        ];
    }
}
