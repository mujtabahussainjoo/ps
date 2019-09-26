<?php
/**
 * @package Adfix_Squarefeed
 * @author  Alona Tsarova
 */

namespace Adfix\Squarefeed\Model\ProductLinks\Types;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped as Type;
use Adfix\Squarefeed\Model\ProductLinks\ProductOptionsInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Grouped implements ProductOptionsInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Grouped constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Prepare product linked options
     *
     * @param string $lastUpdateDate
     * @return array
     */
    public function prepareData($lastUpdateDate = '')
    {
        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToFilter('type_id', ['eq' => Type::TYPE_CODE]);

        if ($lastUpdateDate) {
            $productCollection->addAttributeToFilter('updated_at', ['gteq' => $lastUpdateDate]);
        }

        $groupedData = [];
        foreach ($productCollection as $product) {
            $data = [];
            $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);
            foreach ($associatedProducts as $associatedProduct) {
                $data[][$associatedProduct->getSku()] = [
                    'id' => $associatedProduct->getId(),
                    'price' => $this->priceCurrency->format($associatedProduct->getPrice(), false)
                ];
            }
            $groupedData[$product->getEntityId()]['options'] = $data;
        }

        return $groupedData;
    }
}
