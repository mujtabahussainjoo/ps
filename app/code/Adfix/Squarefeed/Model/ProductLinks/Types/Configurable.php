<?php
/**
 * @package Adfix_Squarefeed
 * @author  Alona Tsarova
 */

namespace Adfix\Squarefeed\Model\ProductLinks\Types;

use Adfix\Squarefeed\Model\ProductLinks\ProductOptionsInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as Type;

class Configurable implements ProductOptionsInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Configurable constructor.
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
        $configurableData = [];
        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToFilter('type_id', ['eq' => Type::TYPE_CODE]);

        if ($lastUpdateDate) {
            $productCollection->addAttributeToFilter('updated_at', ['gteq' => $lastUpdateDate]);
        }

        foreach ($productCollection as $product) {
            $variations = [];
            $variationsLabels = [];
            $productAttributesOptions = $product->getTypeInstance()->getConfigurableOptions($product);
            foreach ($productAttributesOptions as $productAttributeOption) {
                foreach ($productAttributeOption as $optValues) {
                    $variations[$optValues['sku']][$optValues['attribute_code']] = $optValues['option_title'];

                    if (!empty($optValues['super_attribute_label'])) {
                        $variationsLabels[$optValues['attribute_code']] = $optValues['super_attribute_label'];
                    }
                }
            }


            $configurableData[$product->getId()] = [
                'options' => $variations,
                'options_label' => $variationsLabels
            ];
        }

        return $configurableData;
    }
}
