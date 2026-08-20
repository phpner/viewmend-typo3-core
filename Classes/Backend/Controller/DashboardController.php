<?php

declare(strict_types=1);

namespace ViewMend\Typo3Core\Backend\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use ViewMend\Typo3Core\Product\ProductCatalog;

final class DashboardController extends ActionController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly PageRenderer $pageRenderer,
        private readonly ProductCatalog $catalog,
    ) {}

    protected function indexAction(): ResponseInterface
    {
        $products = $this->catalog->products($this->backendUser());
        $installed = count(array_filter($products, static fn(array $product): bool => (bool) $product['installed']));
        $module = $this->moduleTemplateFactory->create($this->request);
        $module->setTitle('ViewMend', 'Dashboard');
        $module->assignMultiple([
            'products' => $products,
            'installedCount' => $installed,
            'availableCount' => count($products) - $installed,
            'composerMode' => Environment::isComposerMode(),
        ]);
        $this->pageRenderer->addCssFile('EXT:viewmend_core/Resources/Public/Css/backend.css');
        $this->pageRenderer->getJavaScriptRenderer()->addJavaScriptModuleInstruction(
            JavaScriptModuleInstruction::create('@viewmend/core/products.js'),
        );

        return $module->renderResponse('Dashboard/Index');
    }

    private function backendUser(): BackendUserAuthentication
    {
        $user = $GLOBALS['BE_USER'] ?? null;
        if (!$user instanceof BackendUserAuthentication) {
            throw new \RuntimeException('No authenticated TYPO3 backend user.', 1787165001);
        }

        return $user;
    }
}
