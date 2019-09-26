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



namespace Mirasvit\SeoMarkup\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CategoryConfig
{
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isRsEnabled($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/category/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isOgEnabled()
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/category/is_og_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isProductOffersEnabled($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/category/is_product_offers_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getDefaultPageSize($store)
    {
        return $this->scopeConfig->getValue(
            'catalog/frontend/grid_per_page',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}