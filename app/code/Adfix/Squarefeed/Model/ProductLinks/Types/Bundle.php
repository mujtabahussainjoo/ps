<?php
/**
 * @package Adfix_Squarefeed
 * @author  Alona Tsarova
 */

namespace Adfix\Squarefeed\Model\ProductLinks\Types;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Adfix\Squarefeed\Model\ProductLinks\ProductOptionsInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Bundle\Model\ResourceModel\Selection\Collection as SelectionCollection;

class Bundle implements ProductOptionsInterface
{
    /**
     * Mapping for price types
     *
     * @var array
     */
    protected $priceTypeMapping = [
        '0' => 'fixed',
        '1' => 'percent'
    ];

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Bundle constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
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
        $bundleData = [];
        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToFilter('type_id', ['eq' => Type::TYPE_CODE]);

        if ($lastUpdateDate) {
            $productCollection->addAttributeToFilter('updated_at', ['gteq' => $lastUpdateDate]);
        }

        foreach ($productCollection as $product) {
            $bundleData[$product->getId()]['options'] = $this->getFormattedBundleOptionValues($product);
        }

        return $bundleData;
    }

    /**
     * Retrieve formatted bundle options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getFormattedBundleOptionValues($product)
    {
        /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
        $optionsCollection = $product->getTypeInstance()
            ->getOptionsCollection($product)
            ->setOrder('position', Collection::SORT_ORDER_ASC);

        $bundleData = [];
        foreach ($optionsCollection as $option) {
            $bundleData[] = [
                'name' => $option->getTitle(),
                'type' => $option->getType(),
                'required' => $option->getRequired(),
                'selections' => $this->getFormattedBundleSelections(
                    $product->getTypeInstance()
                        ->getSelectionsCollection([$option->getId()], $product)
                        ->setOrder('position', Collection::SORT_ORDER_ASC)
                )
            ];
        }

        return $bundleData;
    }

    /**
     * Retrieve option value of bundle product
     *
     * @param \Magento\Bundle\Model\Option $option
     * @return array
     */
    protected function getFormattedOptionValues($option)
    {
        return [
            'name' => $option->getTitle(),
            'type' => $option->getType(),
            'required' => $option->getRequired()
        ];
    }

    /**
     * Retrieve formatted bundle selections
     *
     * @param SelectionCollection $selections
     * @return array
     */
    protected function getFormattedBundleSelections(SelectionCollection $selections)
    {
        $bundleData = [];
        $selections->addAttributeToSort('position');
        foreach ($selections as $selection) {
            $bundleData[$selection->getSku()] = [
                'price' => $selection->getSelectionPriceValue(),
                'default' => $selection->getIsDefault(),
                'default_qty' => $selection->getSelectionQty(),
                'price_type' => $this->getPriceTypeValue($selection->getSelectionPriceType())
            ];
        }

        return $bundleData;
    }

    /**
     * Retrieve bundle price type value by code
     *
     * @param string $type
     * @return string
     */
    protected function getPriceTypeValue($type)
    {
        return isset($this->priceTypeMapping[$type]) ? $this->priceTypeMapping[$type] : null;
    }
}
