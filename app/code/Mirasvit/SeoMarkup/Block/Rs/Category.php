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

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;

class Category extends Template
{
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $category;

    private $categoryConfig;

    private $templateEngineService;

    private $layerResolver;

    private $registry;

    public function __construct(
        CategoryConfig $categoryConfig,
        TemplateEngineServiceInterface $templateEngineService,
        LayerResolver $layerResolver,
        Registry $registry,
        Context $context
    ) {
        $this->categoryConfig        = $categoryConfig;
        $this->templateEngineService = $templateEngineService;
        $this->layerResolver         = $layerResolver;
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
        $this->category = $this->registry->registry('current_category');

        if (!$this->category) {
            return null;
        }

        if (!$this->categoryConfig->isRsEnabled($this->store)) {
            return null;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->layerResolver->get()->getProductCollection();

        if (strripos($collection->getSelect()->__toString(), 'limit') === false) {
            $pageSize = $this->categoryConfig->getDefaultPageSize($this->store);
            $pageNum  = 1;

            if ($toolbar = $this->getLayout()->getBlock('product_list_toolbar')) {
                $pageSize = $toolbar->getLimit();
            }

            if ($pager = $this->getLayout()->getBlock('product_list_toolbar_pager')) {
                $pageNum = $pager->getCurrentPage();
            }

            $collection->setPageSize($pageSize)->setCurPage($pageNum);
        }

        if (!$collection || !$collection->getSize()) {
            return null;
        }

        return [
            '@context'   => 'http://schema.org',
            '@type'      => 'WebPage',
            'url'        => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
            'mainEntity' => [
                '@context'        => 'http://schema.org',
                '@type'           => 'OfferCatalog',
                'name'            => $this->category->getName(),
                'url'             => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
                'numberOfItems'   => $collection->getSize(),
                'itemListElement' => $this->getItemList($collection),
            ],
        ];
    }

    private function getItemList($collection)
    {
        $data = [];

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $item = [
                '@type' => 'Product',
                'name'  => $product->getName(),
                'url'   => $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]),
            ];

            if ($this->categoryConfig->isProductOffersEnabled($this->store)) {
                $offer = $this->getProductOffer($product);

                if ($offer) {
                    $item['offers'] = $offer;
                }
            }

            $data[] = $item;
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array|false
     */
    private function getProductOffer($product)
    {
        $price = $product->getFinalPrice();

        if (!$price) {
            return false;
        }

        $productAvailability = method_exists($product, 'isAvailable')
            ? $product->isAvailable()
            : $product - isInStock();

        if ($productAvailability) {
            $condition = "http://schema.org/InStock";
        } else {
            $condition = "http://schema.org/OutOfStock";
        }

        return [
            '@type'         => 'http://schema.org/Offer',
            'price'         => number_format($price, 2),
            'priceCurrency' => $this->store->getCurrentCurrencyCode(),
            'availability'  => $condition,
        ];
    }
}
