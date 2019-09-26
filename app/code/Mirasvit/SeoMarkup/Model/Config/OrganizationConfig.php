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

class OrganizationConfig
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
            'seo/seo_markup/organization/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomName($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_name',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getCustomName($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_name',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomAddressCountry($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_address_country',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getCustomAddressCountry($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_address_country',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomAddressLocality($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_address_locality',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getCustomAddressLocality($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/address_locality',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomAddressRegion($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_address_region',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getCustomAddressRegion($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_address_region',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomPostalCode($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_postal_code',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return string
     */
    public function getCustomPostalCode($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_postal_code',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomStreetAddress($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_street_address',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getCustomStreetAddress($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_street_address',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomTelephone($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_telephone',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return bool
     */
    public function getCustomTelephone($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_telephone',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return string
     */
    public function getCustomFaxNumber($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_fax_number',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return bool
     */
    public function isCustomEmail($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_email',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getCustomEmail($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_email',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}