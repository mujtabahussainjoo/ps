<?php
/**
 * @package Adfix_Squarefeed
 * @author  Alona Tsarova
 */

namespace Adfix\Squarefeed\Model;

use Magento\Store\Model\Store;
use Adfix\Squarefeed\Logger\Logger;
use Adfix\Squarefeed\Api\ProductInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductColFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryColFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttrSetColFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeColFactory;

class Product implements ProductInterface
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * Pairs of attribute set ID-to-name.
     *
     * @var array
     */
    protected $attrSetIdToName = [];

    /**
     * @var AttrSetColFactory
     */
    protected $attrSetColFactory;
    /**
     * Attribute code to its values. Only attributes with options and only default store values used.
     *
     * @var array
     */
    protected $attributeValues = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected $attributeColFactory;

    /**
     * Categories ID to text-path hash.
     *
     * @var array
     */
    protected $categories = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * Product constructor.
     *
     * @param Logger $logger
     * @param DateTime $dateTime
     * @param ProductFactory $productFactory
     * @param UrlFinderInterface $urlFinder
     * @param ProductColFactory $productColFactory
     * @param AttrSetColFactory $attrSetColFactory
     * @param CategoryColFactory $categoryColFactory
     * @param AttributeColFactory $attributeColFactory
     */
    public function __construct(
        Logger $logger,
        DateTime $dateTime,
        ProductFactory $productFactory,
        UrlFinderInterface $urlFinder,
        ProductColFactory $productColFactory,
        AttrSetColFactory $attrSetColFactory,
        CategoryColFactory $categoryColFactory,
        AttributeColFactory $attributeColFactory
    ) {
        $this->date = $dateTime;
        $this->logger = $logger;
        $this->urlFinder = $urlFinder;
        $this->productFactory = $productFactory;
        $this->attrSetColFactory = $attrSetColFactory;
        $this->productCollection = $productColFactory->create();
        $this->categoryCollection = $categoryColFactory->create();
        $this->attributeColFactory = $attributeColFactory->create();

        $this->initAttributes()
            ->initCategories()
            ->initAttributeSets();
    }

    /**
     * Retrieve product's list
     *
     * @param int $lastUpdateTime
     * @param int $currentPage
     * @param int $pageSize
     * @return array
     */
    public function getList($lastUpdateTime = 0, $currentPage = 0, $pageSize = 0)
    {
        $this->productCollection->addAttributeToSelect('*');
        $this->productCollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $this->productCollection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        if ((int)$lastUpdateTime > 0) {
            $formatDate = $this->date->gmtDate(null, (int)$lastUpdateTime);
            $this->productCollection->addAttributeToFilter('updated_at', ['gteq' => $formatDate]);
        }
        if ((int)$currentPage !== 0 && (int)$pageSize !== 0) {
            $this->productCollection->setCurPage((int)$currentPage);
            $this->productCollection->setPageSize((int)$pageSize);
        }

        $productsTotal = $this->productCollection->getSize();
        $response = [
            'status' => 'OK',
            'total' => $productsTotal,
            'page' => ((int)$currentPage === 0) ? 1 : (int)$currentPage,
            'pageSize' => ((int)$pageSize === 0) ? $productsTotal : (int)$pageSize
        ];

        try {
            $this->productCollection->load();
            $this->productCollection->addCategoryIds();
            $this->productCollection->addTierPriceData();

            /** @var \Magento\Catalog\Model\Product $item */
            foreach ($this->productCollection->getItems() as $item) {
                $itemData = $this->addAttributeValues($item->getData());
                $itemData = $this->addCategoriesPath($itemData);
                $itemData = $this->addAttributeSetName($itemData);
                $itemData['url_key'] = $this->getProductPath($item);
                $response['products'][] = $itemData;
            }
        } catch (\Exception $e) {
            $response['status'] = 'FAILED';
            $response['message'] = $e->getMessage() . '. For more info please check magento squarefeed log file.';
            $this->logger->info('[WebsiteProducts] ERROR: ' . $e->getMessage());
            $this->logger->info('Line - ' . $e->getLine() . ', ' . $e->getFile());
            $this->logger->info($e->getTraceAsString());
        }

        $response['timestamp'] = $this->date->gmtTimestamp();
        return [$response];
    }

    /**
     * Retrieve product requested path
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getProductPath($product)
    {
        $requestPath = $product->getRequestPath();
        if ($requestPath) {
            return $requestPath;
        }

        $filterData = [
            UrlRewrite::ENTITY_ID => $product->getId(),
            UrlRewrite::ENTITY_TYPE => \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::STORE_ID => $product->getStoreId(),
        ];
        $rewrite = $this->urlFinder->findOneByData($filterData);
        if ($rewrite) {
            return $rewrite->getRequestPath();
        }

        return '';
    }

    /**
     * Add attributes values instead of id
     *
     * @param array $itemData
     * @return array
     */
    protected function addAttributeValues($itemData = [])
    {
        foreach ($this->attributeValues as $code => $options) {
            if (!key_exists($code, $itemData)) {
                continue;
            }
            $values = [];
            foreach (explode(',', $itemData[$code]) as $valueId) {
                if (!isset($options[$valueId])) {
                    continue;
                }
                $values[] = $options[$valueId];
            }
            $itemData[$code] = implode(',', $values);
        }
        return $itemData;
    }

    /**
     * Add full categories path
     *
     * @param $itemData
     * @return mixed
     */
    protected function addCategoriesPath($itemData)
    {
        if (!isset($itemData['category_ids']) || !is_array($itemData['category_ids'])) {
            return $itemData;
        }

        $categoryPaths = [];
        foreach ($itemData['category_ids'] as $categoryId) {
            if (!key_exists($categoryId, $this->categories)) {
                continue;
            }
            $categoryPaths[] = $this->categories[$categoryId];
        }
        $itemData['categories'] = implode(',', $categoryPaths);
        unset($itemData['category_ids']);
        return $itemData;
    }

    /**
     * Add attribute set name to the items data
     *
     * @param array $itemData
     * @return mixed
     */
    protected function addAttributeSetName($itemData)
    {
        if (!isset($itemData['attribute_set_id'])) {
            return $itemData;
        }

        $attrSetId = $itemData['attribute_set_id'];
        if (!isset($this->attrSetIdToName[$attrSetId])) {
            return $itemData;
        }
        $itemData['attribute_set_name'] = $this->attrSetIdToName[$attrSetId];
        return $itemData;
    }

    /**
     * Init select and multiselect attributes
     *
     * @return $this
     */
    protected function initAttributes()
    {
        foreach ($this->attributeColFactory as $attribute) {
            /**
             * If attribute isn't select or multi select, no needs to load it
             */
            if (!in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
                continue;
            }

            $this->attributeValues[$attribute->getAttributeCode()] = $this->getAttributeOptions($attribute);
        }
        return $this;
    }

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased.
     *
     * @param AbstractAttribute $attribute
     * @return array
     */
    protected function getAttributeOptions(AbstractAttribute $attribute)
    {
        if (!$attribute->usesSource()) {
            return [];
        }

        $options = [];
        $attribute->setStoreId(Store::DEFAULT_STORE_ID);
        try {
            foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                foreach (is_array($option['value']) ? $option['value'] : [$option] as $innerOption) {
                    if (!strlen($innerOption['value'])) {
                        continue;
                    }
                    $options[$innerOption['value']] = (string)$innerOption['label'];
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('[Product.getAttributeOptions] ERROR: ' . $e->getMessage());
            $this->logger->info($e->getTraceAsString());
        }

        return $options;
    }

    /**
     * Init categories
     *
     * @return $this
     */
    protected function initCategories()
    {
        $collection = $this->categoryCollection->addNameToResult();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        foreach ($collection as $category) {
            $structure = preg_split('#/+#', $category->getPath());
            $pathSize = count($structure);
            if ($pathSize > 1) {
                $path = [];
                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $collection->getItemById($structure[$i])->getName();
                }
                if ($pathSize > 2) {
                    $this->categories[$category->getId()] = implode('/', $path);
                }
            }
        }
        return $this;
    }

    /**
     * Initialize attribute sets code-to-id pairs.
     *
     * @return $this
     */
    protected function initAttributeSets()
    {
        $productTypeId = $this->productFactory->create()->getTypeId();
        foreach ($this->attrSetColFactory->create()->setEntityTypeFilter($productTypeId) as $attributeSet) {
            $this->attrSetIdToName[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
        }
        return $this;
    }
}
