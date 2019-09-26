<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.146
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoMarkup\Block\Og;

use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\PageConfig;

class Page extends AbstractBlock
{
    private $config;

    private $stateService;

    private $logo;

    public function __construct(
        PageConfig $pageConfig,
        StateServiceInterface $stateService,
        Logo $logo,
        Template\Context $context
    ) {
        $this->config       = $pageConfig;
        $this->stateService = $stateService;
        $this->logo         = $logo;

        parent::__construct($context);
    }

    protected function getMeta()
    {
        if (!$this->config->isOgEnabled()) {
            return false;
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeManager->getStore();

        $meta = [
            'og:type'        => $this->stateService->isHomePage() ? 'website' : 'article',
            'og:url'         => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
            'og:title'       => $this->pageConfig->getTitle()->get(),
            'og:description' => $this->pageConfig->getDescription(),
            'og:image'       => $this->logo->getLogoSrc(),
            'og:site_name'   => $store->getFrontendName(),
        ];

        return $meta;
    }
}
