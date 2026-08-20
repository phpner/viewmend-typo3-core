# ViewMend Core for TYPO3

ViewMend Core provides the shared TYPO3 backend shell for independently
installed ViewMend products. It owns the top-level **ViewMend** module, the
product Dashboard, and the small public contract products use to register
themselves.

Core is intentionally product-neutral. It contains no form processing, mail,
monitoring, submission storage, or cloud integration logic. Each product stays
in its own Composer package and Git repository and can be installed, upgraded,
or removed independently.

## Requirements

- PHP 8.2, 8.3, 8.4, or 8.5
- TYPO3 13.4 LTS or TYPO3 14.3
- Composer-based TYPO3 installation is recommended

## Installation

ViewMend products normally install Core as a dependency. Once the package is
available through the project's configured Composer repositories, it can also
be added directly:

```bash
composer require viewmend/typo3-core
vendor/bin/typo3 extension:setup
```

After setup, open **ViewMend → Dashboard** in the TYPO3 backend.

The Dashboard never changes Composer dependencies from a backend request. It
only presents copyable installation commands. In classic installations,
extension management remains delegated to TYPO3's Extension Manager.

## Dashboard behavior

The Dashboard separates products that are ready to use from products that are
available to install:

- installed products appear first and link to their own backend module;
- TYPO3 module permissions determine whether the current backend user can open
  an installed product;
- optional operational metrics are resolved for the current backend user;
- unavailable or unpublished products never receive a non-working install
  action;
- a failing product summary is logged and isolated so the shared Dashboard
  remains available.

Core ships only the catalog and registration contract. It never calls product
services to process submissions, deliver mail, run monitoring, or perform
other product work.

## Registering a product

Every product must:

1. require `viewmend/typo3-core`;
2. register its own TYPO3 backend module as a direct child of `viewmend`;
3. provide one `ProductProviderInterface` service tagged `viewmend.product`;
4. avoid dependencies on other ViewMend products.

A minimal provider looks like this:

```php
<?php

declare(strict_types=1);

namespace Vendor\Product\Integration\ViewMend;

use ViewMend\Typo3Core\Contract\ProductProviderInterface;
use ViewMend\Typo3Core\Product\ProductDefinition;

final readonly class ProductProvider implements ProductProviderInterface
{
    public function product(): ProductDefinition
    {
        return new ProductDefinition(
            identifier: 'example-product',
            title: 'Example product',
            description: 'A concise description of the product outcome.',
            composerPackage: 'vendor/typo3-example-product',
            extensionKey: 'example_product',
            moduleIdentifier: 'viewmend_example_product',
            category: 'Operations',
            position: 100,
            iconIdentifier: 'example-product-module-icon',
        );
    }
}
```

Register the provider explicitly in the product's `Configuration/Services.yaml`:

```yaml
services:
  Vendor\Product\Integration\ViewMend\ProductProvider:
    tags:
      - name: viewmend.product
```

The product module uses the shared parent identifier:

```php
return [
    'viewmend_example_product' => [
        'parent' => 'viewmend',
        'access' => 'user',
        'path' => '/module/viewmend/example-product',
        // Product-specific module configuration follows.
    ],
];
```

## Optional Dashboard data

A provider may implement `ProductDashboardProviderInterface` to expose a small
permission-aware summary. Values must be operational facts that can be loaded
quickly; the Dashboard is not a reporting or analytics transport.

```php
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use ViewMend\Typo3Core\Contract\ProductDashboardProviderInterface;
use ViewMend\Typo3Core\Product\ProductDashboardData;
use ViewMend\Typo3Core\Product\ProductMetric;

public function dashboardData(BackendUserAuthentication $user): ProductDashboardData
{
    return new ProductDashboardData(
        metadata: ['3 active rules'],
        metrics: [new ProductMetric('Accepted · 24h', '18')],
    );
}
```

Dashboard data must respect the current backend user's permissions and must not
expose records from inaccessible sites or modules.

## Development

Install development dependencies in a standalone checkout, then run:

```bash
composer test
composer analyse
composer lint
vendor/bin/yaml-lint --parse-tags Configuration
```

The CI matrix covers the supported TYPO3 and PHP boundaries.

## Support

- Issues: <https://github.com/phpner/viewmend-typo3-core/issues>
- Source: <https://github.com/phpner/viewmend-typo3-core>
- ViewMend: <https://viewmend.com/>

## License

ViewMend Core is released under the
[GNU General Public License 2.0 or later](LICENSE).
