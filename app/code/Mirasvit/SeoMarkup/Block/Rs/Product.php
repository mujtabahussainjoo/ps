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



namespace Mirasvit\SeoMarkup\Block\Rs;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class Product extends Template
{
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    private $productConfig;

    private $templateEngineService;

    private $imageHelper;

    private $offerData;

    private $aggregateOfferData;

    private $reviewData;

    private $ratingData;

    private $registry;

    public function __construct(
        ProductConfig $productConfig,
        TemplateEngineServiceInterface $templateEngineService,
        Product\OfferData $offerData,
        Product\AggregateOfferData $aggregateOfferData,
        Product\ReviewData $reviewData,
        Product\RatingData $ratingData,
        ImageHelper $imageHelper,
        Registry $registry,
        Context $context
    ) {
        $this->productConfig         = $productConfig;
        $this->templateEngineService = $templateEngineService;
        $this->offerData             = $offerData;
        $this->aggregateOfferData    = $aggregateOfferData;
        $this->reviewData            = $reviewData;
        $this->ratingData            = $ratingData;
        $this->imageHelper           = $imageHelper;
        $this->store                 = $context->getStoreManager()->getStore();
        $this->registry              = $registry;

        parent::__construct($context);
    }

    protected function _toHtml()
    {
        $data = $this->getJsonData();

        if (!$data) {
            return false;
        }

        return '<script type="application/ld+json">' . \Zend_Json::encode($data) . '</script>';
    }


    /**
     * @return bool|array
     */
    public function getJsonData()
    {
        $this->product = $this->registry->registry('current_product');

        if (!$this->product) {
            return null;
        }

        $offer = $this->product->getTypeId() == 'configurable'
            ? $this->aggregateOfferData->getData($this->product, $this->store)
            : $this->offerData->getData($this->product, $this->store);

        $values = [
            '@context'        => 'http://schema.org',
            '@type'           => 'Product',
            'name'            => $this->templateEngineService->render('[product_name]'),
            'sku'             => $this->templateEngineService->render('[product_sku]'),
            'mpn'             => $this->getManufacturerPartNumber(),
            'image'           => $this->getImage(),
            'category'        => $this->getCategoryName(),
            'brand'           => $this->getBrand(),
            'model'           => $this->getModel(),
            'color'           => $this->getColor(),
            'weight'          => $this->getWeight(),
            'width'           => $this->getDimensionValue('width'),
            'height'          => $this->getDimensionValue('height'),
            'depth'           => $this->getDimensionValue('depth'),
            'description'     => $this->getDescription(),
            'gtin8'           => $this->getGtinValue(8),
            'gtin12'          => $this->getGtinValue(12),
            'gtin13'          => $this->getGtinValue(13),
            'gtin14'          => $this->getGtinValue(14),
            'offers'          => $offer,
            'review'          => $this->reviewData->getData($this->product, $this->store),
            'aggregateRating' => $this->ratingData->getData($this->product, $this->store),
        ];

        $data = [];

        foreach ($values as $key => $value) {
            if ($value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @return string|false
     */
    private function getManufacturerPartNumber()
    {
        if ($this->productConfig->isMpnEnabled()) {
            return $this->templateEngineService->render('[product_sku]');
        }

        return false;
    }

    /**
     * @return string|false
     */
    private function getImage()
    {
        if ($this->productConfig->isImageEnabled()) {
            return $this->imageHelper->init($this->product, 'product_page_image_large')->getUrl();
        }

        return false;
    }

    /**
     * @return string|false
     */
    private function getCategoryName()
    {
        if (!$this->productConfig->isCategoryEnabled()) {
            return false;
        }

        return $this->templateEngineService->render("[product_category_name]");
    }

    /**
     * @return string|false
     */
    private function getBrand()
    {
        if ($attribute = $this->productConfig->getBrandAttribute()) {
            return $this->templateEngineService->render("[product_$attribute]");
        }

        return false;
    }

    /**
     * @return string|false
     */
    private function getModel()
    {
        if ($attribute = $this->productConfig->getModelAttribute()) {
            return $this->templateEngineService->render("[product_$attribute]");
        }

        return false;
    }

    /**
     * @return string|false
     */
    private function getColor()
    {
        if ($attribute = $this->productConfig->getColorAttribute()) {
            return $this->templateEngineService->render("[product_$attribute]");
        }

        return false;
    }

    /**
     * @return array|false
     */
    private function getWeight()
    {
        $unitCode = $this->productConfig->getWeightUnitType();

        if (!$unitCode) {
            return false;
        }

        $value = $this->templateEngineService->render('[product_weight]');

        if (!$value) {
            return false;
        }

        return [
            '@type'    => 'QuantitativeValue',
            'value'    => $value,
            'unitCode' => $unitCode,
        ];
    }

    /**
     * @return false|string
     */
    private function getDescription()
    {
        $value = false;

        if ($this->productConfig->getDescriptionType() == ProductConfig::DESCRIPTION_TYPE_DESCRIPTION) {
            $value = $this->templateEngineService->render("[product_description]");
        }

        if ($this->productConfig->getDescriptionType() == ProductConfig::DESCRIPTION_TYPE_META) {
            $value = $this->templateEngineService->render("[page_meta_description]");
        }

        if ($this->productConfig->getDescriptionType() == ProductConfig::DESCRIPTION_TYPE_SHORT_DESCRIPTION) {
            $value = $this->templateEngineService->render("[product_short_description]");
        }

        return $value ? strip_tags($value) : false;
    }

    /**
     * @param string $type
     *
     * @return array|false
     */
    private function getDimensionValue($type)
    {
        if (!$this->productConfig->isDimensionsEnabled()) {
            return false;
        }

        $unitCode = $this->productConfig->getDimensionUnit();

        if (!$unitCode) {
            return false;
        }

        switch ($type) {
            case 'width':
                $attribute = $this->productConfig->getDimensionWidthAttribute();
                break;

            case 'height':
                $attribute = $this->productConfig->getDimensionHeightAttribute();
                break;

            case 'depth':
                $attribute = $this->productConfig->getDimensionDepthAttribute();
                break;

            default:
                $attribute = false;
        }

        if (!$attribute) {
            return false;
        }

        $value = $this->templateEngineService->render("[product_$attribute]");

        if (!$value) {
            return false;
        }

        return [
            '@type'    => 'QuantitativeValue',
            'value'    => $value,
            'unitCode' => $unitCode,
        ];
    }

    /**
     * @param int $number
     *
     * @return false|string
     */
    private function getGtinValue($number)
    {
        switch ($number) {
            case 8:
                $attribute = $this->productConfig->getGtin8Attribute();
                break;

            case 12:
                $attribute = $this->productConfig->getGtin12Attribute();
                break;

            case 13:
                $attribute = $this->productConfig->getGtin13Attribute();
                break;

            case 14:
                $attribute = $this->productConfig->getGtin14Attribute();
                break;

            default:
                $attribute = false;
        }

        if (!$attribute) {
            return false;
        }

        return $this->templateEngineService->render("[product_$attribute]");
    }
}
