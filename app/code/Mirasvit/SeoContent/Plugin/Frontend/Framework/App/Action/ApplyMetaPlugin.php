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



namespace Mirasvit\SeoContent\Plugin\Frontend\Framework\App\Action;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Mirasvit\SeoContent\Service\ContentService;
use Mirasvit\SeoContent\Service\StateService;

class ApplyMetaPlugin
{
    private $contentService;

    private $pageConfig;

    private $layout;

    public function __construct(
        ContentService $contentService,
        PageConfig $pageConfig,
        LayoutInterface $layout
    ) {
        $this->contentService = $contentService;
        $this->pageConfig     = $pageConfig;
        $this->layout         = $layout;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param object                                 $response
     *
     * @return object
     */
    public function afterDispatch($subject, $response)
    {
        if ($subject->getRequest()->isAjax() || $subject instanceof \Magento\Framework\App\Action\Forward) {
            return $response;
        }

        if (!$this->contentService->isProcessablePage()) {
            return $response;
        }

        $content = $this->contentService->getCurrentContent();

        if ($content->getTitle()) {
            $this->pageConfig->getTitle()->set($content->getTitle());
        }

        # 2.3+
        if ($content->getMetaTitle() && method_exists($this->pageConfig, 'setMetaTitle')) {
            $this->pageConfig->setMetaTitle($content->getMetaTitle());
        }

        if ($content->getMetaDescription()) {
            $this->pageConfig->setDescription($content->getMetaDescription());
        }

        if ($content->getMetaKeywords()) {
            $this->pageConfig->setKeywords($content->getMetaKeywords());
        }

        if ($content->getTitle()) {
            /** @var \Magento\Theme\Block\Html\Title $titleBlock */
            $titleBlock = $this->layout->getBlock('page.main.title');

            if ($titleBlock) {
                $titleBlock->setPageTitle($content->getTitle());
            }
        }

        return $response;
    }
}
