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

class TwitterConfig
{
    const CARD_TYPE_SMALL_IMAGE = 1;
    const CARD_TYPE_LARGE_IMAGE = 2;

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/twitter/card_type');
    }

    /**
     * @return bool
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/twitter/username');
    }
}