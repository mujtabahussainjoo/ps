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



namespace Mirasvit\SeoContent\Service\Content\Modifier;

use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Model\Config;

/**
 * Purpose: Rewrite SEO meta, if product/category already have own meta
 */
class RestoreMetaModifier implements ModifierInterface
{
    private $stateService;

    private $config;

    public function __construct(
        StateServiceInterface $stateService,
        Config $config
    ) {
        $this->stateService = $stateService;
        $this->config       = $config;
    }

    public function modify(ContentInterface $content)
    {
        if ($this->stateService->isProductPage() && $this->config->useProductMetaTags()) {
            $product = $this->stateService->getProduct();

            $this->setMeta($content, ContentInterface::META_TITLE, $product->getData('meta_title'));
            $this->setMeta($content, ContentInterface::META_KEYWORDS, $product->getData('meta_keyword'));
            $this->setMeta($content, ContentInterface::META_DESCRIPTION, $product->getData('meta_description'));
        } elseif ($this->stateService->isCategoryPage() && $this->config->useCategoryMetaTags()) {
            $category = $this->stateService->getCategory();

            $this->setMeta($content, ContentInterface::META_TITLE, $category->getData('meta_title'));
            $this->setMeta($content, ContentInterface::META_KEYWORDS, $category->getData('meta_keyword'));
            $this->setMeta($content, ContentInterface::META_DESCRIPTION, $category->getData('meta_description'));
            $this->setMeta($content, ContentInterface::CATEGORY_DESCRIPTION, $category->getData('description'));
        }

        if ($this->stateService->isCategoryPage()) {
            $category = $this->stateService->getCategory();

            $title = $category->getData('title_h1');

            if ($title) {
                $this->setMeta($content, ContentInterface::TITLE, $title);
            }
        }

        return $content;
    }

    private function setMeta(ContentInterface $content, $property, $value)
    {
        if (!$value) {
            return;
        }

        $content->setData($property, $value);

        $content->setData(
            $property . '_TOOLBAR',
            "Default $property"
        );
    }
}
