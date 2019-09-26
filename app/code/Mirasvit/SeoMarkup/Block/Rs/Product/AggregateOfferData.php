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



namespace Mirasvit\SeoMarkup\Block\Rs\Product;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Shipping\Model\Config as ShippingConfig;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class AggregateOfferData
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    private $productConfig;

    private $templateEngineService;

    private $paymentConfig;

    private $offerData;

    private $scopeConfig;

    private $shippingConfig;

    public function __construct(
        ProductConfig $productConfig,
        OfferData $offerData,
        TemplateEngineServiceInterface $templateEngineService,
        PaymentConfig $paymentConfig,
        ScopeConfigInterface $scopeConfig,
        ShippingConfig $shippingConfig
    ) {
        $this->productConfig         = $productConfig;
        $this->offerData             = $offerData;
        $this->templateEngineService = $templateEngineService;
        $this->paymentConfig         = $paymentConfig;
        $this->scopeConfig           = $scopeConfig;
        $this->shippingConfig        = $shippingConfig;
    }

    /**
     * @param object $product
     * @param object $store
     *
     * @return array|false
     */
    public function getData($product, $store)
    {
        $this->product = $product;
        $this->store   = $store;

        $values = [
            '@type'         => 'AggregateOffer',
            'lowPrice'      => 0,
            'highPrice'     => 0,
            'priceCurrency' => $this->store->getCurrentCurrencyCode(),
            'offers'        => [],
        ];

        $minPrice = 0;
        $maxPrice = 0;

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $this->product->getTypeInstance();

        $child = $typeInstance->getUsedProductCollection($this->product)
            ->addAttributeToSelect('visibility')
            ->addPriceData();

        foreach ($child as $item) {
            $offer = $this->offerData->getData($item, $this->store);
            if (!$offer) {
                continue;
            }

            $minPrice = $minPrice == 0 ? $offer['price'] : min($minPrice, $offer['price']);
            $maxPrice = max($maxPrice, $offer['price']);

            $values['offers'][] = $offer;
        }

        $values['lowPrice']   = $minPrice;
        $values['highPrice']  = $maxPrice;
        $values['offerCount'] = count($values['offers']);

        if (!$values['lowPrice'] || !$values['offerCount']) {
            return $this->offerData->getData($product, $store);
        }

        return $values;
    }

    /**
     * @return string|false
     */
    private function getOfferAvailability()
    {
        if (!$this->productConfig->isAvailabilityEnabled()) {
            return false;
        }

        $productAvailability = method_exists($this->product, 'isAvailable')
            ? $this->product->isAvailable()
            : $this->product->isInStock();

        if ($productAvailability) {
            return "http://schema.org/InStock";
        } else {
            return "http://schema.org/OutOfStock";
        }
    }

    /**
     * @return string|false
     */
    private function getOfferItemCondition()
    {
        $conditionType = $this->productConfig->getItemConditionType();

        if (!$conditionType) {
            return false;
        }

        if ($conditionType == ProductConfig::ITEM_CONDITION_NEW_ALL) {
            return "http://schema.org/NewCondition";
        } elseif ($conditionType == ProductConfig::ITEM_CONDITION_MANUAL) {
            $attribute      = $this->productConfig->getItemConditionAttribute();
            $conditionValue = $this->templateEngineService->render("[product_$attribute]");

            if (!$conditionValue) {
                return false;
            }

            switch ($conditionValue) {
                case $this->productConfig->getItemConditionAttributeValueNew():
                    return "http://schema.org/NewCondition";

                case $this->productConfig->getItemConditionAttributeValueUsed():
                    return "http://schema.org/UsedCondition";

                case $this->productConfig->getItemConditionAttributeValueRefurbished():
                    return "http://schema.org/RefurbishedCondition";

                case $this->productConfig->getItemConditionAttributeValueDamaged():
                    return "http://schema.org/DamagedCondition";
            }
        }

        return false;
    }

    /**
     * @return array|false
     */
    private function getOfferAcceptedPaymentMethods()
    {
        if (!$this->productConfig->isAcceptedPaymentMethodEnabled()) {
            return false;
        }

        if ($activePaymentMethods = $this->getActivePaymentMethods()) {
            return $activePaymentMethods;
        }

        return false;
    }

    /**
     * @return array|false
     */
    protected function getOfferAvailableDeliveryMethods()
    {
        if (!$this->productConfig->isAvailableDeliveryMethodEnabled()) {
            return false;
        }

        if ($activeDeliveryMethods = $this->getActiveDeliveryMethods()) {
            return $activeDeliveryMethods;
        }

        return false;
    }

    /**
     * @return array
     */
    private function getActiveDeliveryMethods()
    {
        $methods = [];

        $allMethods = [
            'flatrate'     => 'DeliveryModeFreight',
            'freeshipping' => 'DeliveryModeFreight',
            'tablerate'    => 'DeliveryModeFreight',
            'dhl'          => 'DHL',
            'fedex'        => 'FederalExpress',
            'ups'          => 'UPS',
            'usps'         => 'DeliveryModeMail',
            'dhlint'       => 'DHL',
        ];

        $deliveryMethods = $this->shippingConfig->getActiveCarriers();
        foreach (array_keys($deliveryMethods) as $code) {
            if (isset($allMethods[$code])) {
                $methods[] = 'http://purl.org/goodrelations/v1#' . $allMethods[$code];
            }
        }

        return array_unique($methods);
    }

    /**
     * @return array
     */
    private function getActivePaymentMethods()
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods  = [];
        foreach (array_keys($payments) as $paymentCode) {
            if (strpos($paymentCode, 'paypal') !== false) {
                $methods[] = 'http://purl.org/goodrelations/v1#PayPal';
            }

            if (strpos($paymentCode, 'googlecheckout') !== false) {
                $methods[] = 'http://purl.org/goodrelations/v1#GoogleCheckout';
            }

            if (strpos($paymentCode, 'cash') !== false) {
                $methods[] = 'http://purl.org/goodrelations/v1#Cash';
            }

            if ($paymentCode == 'ccsave') {
                if ($existingMethods = $this->getActivePaymentCCTypes()) {
                    $methods = array_merge($methods, $existingMethods);
                }
            }
        }

        return array_unique($methods);
    }

    /**
     * @return array|bool
     */
    private function getActivePaymentCCTypes()
    {
        $methods    = [];
        $allMethods = [
            'AE'  => 'AmericanExpress',
            'VI'  => 'VISA',
            'MC'  => 'MasterCard',
            'DI'  => 'Discover',
            'JCB' => 'JCB',
        ];

        $ccTypes = $this->scopeConfig->getValue(
            'payment/ccsave/cctypes',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->store
        );

        if ($ccTypes) {
            $list = explode(',', $ccTypes);

            foreach ($list as $value) {
                if (isset($allMethods[$value])) {
                    $methods[] = 'http://purl.org/goodrelations/v1#' . $allMethods[$value];
                }
            }

            return $methods;
        }

        return false;
    }
}