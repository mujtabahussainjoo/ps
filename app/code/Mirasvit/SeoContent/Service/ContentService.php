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



namespace Mirasvit\SeoContent\Service;

use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Service\Content\Modifier\ModifierInterface;

class ContentService
{
    private $isProcessed = false;

    private $templateService;

    private $rewriteService;

    private $stateService;

    private $content;

    private $templateEngineService;

    /**
     * @var ModifierInterface[]
     */
    private $modifierPool;

    public function __construct(
        TemplateService $templateService,
        RewriteService $rewriteService,
        StateServiceInterface $stateService,
        ContentInterface $content,
        TemplateEngineServiceInterface $templateEngineService,
        array $modifierPool
    ) {
        $this->templateService = $templateService;
        $this->rewriteService  = $rewriteService;
        $this->stateService    = $stateService;

        $this->content               = $content;
        $this->templateEngineService = $templateEngineService;

        $this->modifierPool = $modifierPool;
    }

    public function isProcessablePage()
    {
        if ($this->stateService->isCategoryPage()
            || $this->stateService->isProductPage()
            || $this->rewriteService->getRewrite(null)
        ) {
            return true;
        }

        return false;
    }

    public function putDefaultMeta(array $meta)
    {
        foreach ($meta as $property => $value) {
            $this->content->setData($property, $value);
        }

        $this->isProcessed = false;
    }

    public function getCurrentContent()
    {
        if ($this->isProcessed) {
            return $this->content;
        }

        $ruleType = $this->getRuleType();

        $template = $this->templateService->getTemplate(
            $ruleType,
            $this->stateService->getCategory(),
            $this->stateService->getProduct(),
            $this->stateService->getFilters()
        );

        $rewrite = $this->rewriteService->getRewrite(null);

        if ($template) {
            $this->content->setData(ContentInterface::DESCRIPTION_POSITION, $template->getDescriptionPosition());
            $this->content->setData(ContentInterface::DESCRIPTION_TEMPLATE, $template->getDescriptionTemplate());
            $this->content->setData(ContentInterface::APPLIED_TEMPLATE_ID, $template->getId());
        }

        if ($rewrite) {
            $this->content->setData(ContentInterface::APPLIED_REWRITE_ID, $rewrite->getId());
            $this->content->setData(ContentInterface::DESCRIPTION_POSITION, $rewrite->getDescriptionPosition());
            $this->content->setData(ContentInterface::DESCRIPTION_TEMPLATE, $rewrite->getDescriptionTemplate());
        }

        $properties = [
            ContentInterface::TITLE,
            ContentInterface::META_TITLE,
            ContentInterface::META_KEYWORDS,
            ContentInterface::META_DESCRIPTION,
            ContentInterface::DESCRIPTION,
            ContentInterface::SHORT_DESCRIPTION,
            ContentInterface::FULL_DESCRIPTION,
            ContentInterface::CATEGORY_DESCRIPTION,
        ];

        foreach ($properties as $property) {
            $rewriteValue = $rewrite ? $rewrite->getData($property) : false;

            if ($rewriteValue) {
                $this->content->setData($property, $rewriteValue);

                $this->content->setData(
                    $property . '_TOOLBAR',
                    "Rewrite #{$rewrite->getId()}"
                );

                continue;
            }

            $templateValue = $template ? $template->getData($property) : false;

            if ($templateValue) {
                $this->content->setData($property, $templateValue);

                $this->content->setData(
                    $property . '_TOOLBAR',
                    "Template #{$template->getId()}"
                );
            }
        }

        foreach ($properties as $property) {
            $this->content->setData($property, $this->templateEngineService->render(
                $this->content->getData($property)
            ));
        }

        foreach ($this->modifierPool as $modifier) {
            $this->content = $modifier->modify($this->content);
        }

        $this->isProcessed = true;

        return $this->content;
    }

    /**
     * @return int
     */
    private function getRuleType()
    {
        if ($this->stateService->isProductPage()) {
            return TemplateInterface::RULE_TYPE_PRODUCT;
        } elseif ($this->stateService->isNavigationPage()) {
            return TemplateInterface::RULE_TYPE_NAVIGATION;
        } elseif ($this->stateService->isCategoryPage()) {
            return TemplateInterface::RULE_TYPE_CATEGORY;
        }

        return 0;
    }
}
