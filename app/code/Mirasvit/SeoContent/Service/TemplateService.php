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

use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;

class TemplateService
{
    private $templateRepository;

    private $storeManager;

    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->templateRepository = $templateRepository;
        $this->storeManager       = $storeManager;
    }

    /**
     * @param int                                   $ruleType
     * @param \Magento\Catalog\Model\Category|false $category
     * @param \Magento\Catalog\Model\Product|false  $product
     * @param DataObject|false                      $filterData
     *
     * @return bool|TemplateInterface
     */
    public function getTemplate($ruleType, $category, $product, $filterData)
    {
        $collection = $this->templateRepository->getCollection();
        $collection->addFieldToFilter(TemplateInterface::IS_ACTIVE, true)
            ->addFieldToFilter(TemplateInterface::RULE_TYPE, $ruleType)
            ->addStoreFilter($this->storeManager->getStore())
            ->setOrder(TemplateInterface::SORT_ORDER, 'desc');

        foreach ($collection as $template) {
            $isFollowRules = true;

            if ($category) {
                $isFollowRules = $isFollowRules && $template->getRule()->validate($category);
                if (!$isFollowRules && $template->isApplyForChildCategories()) {
                    $parent = $category->getParentCategory();

                    $counter = 0;
                    while (
                        $parent
                        && $parent->getParentId() > 0
                        && !$isFollowRules
                        && $counter < 5
                    ) {
                        $isFollowRules = $template->getRule()->validate($parent);
                        $parent        = $parent->getParentCategory();
                        $counter++;
                    }
                }
            }

            if ($product) {
                $isFollowRules = $isFollowRules && $template->getRule()->validate($product);
            }

            if ($filterData) {
                $isFollowRules = $isFollowRules && $template->getRule()->validate($filterData);
            }

            if ($isFollowRules) {
                return $template;
            }
        }

        return false;
    }
}
