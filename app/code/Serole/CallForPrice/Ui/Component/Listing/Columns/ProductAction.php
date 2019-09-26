<?php
namespace Serole\CallForPrice\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ProductAction extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /** Url Path */
    const CUSTOMER_URL_PATH_EDIT = 'catalog/product/index/edit/id/';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = array(),
        UrlInterface $urlBuilder,
        \Magento\Catalog\Model\Product $product,
        array $data = array()) 
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->product = $product;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $productName = $this->getProductName($item[$name]);  
                if (isset($item['product_id'])) {                    
                    $item[$name] = html_entity_decode('<a href="'.$this->urlBuilder->getUrl(self::CUSTOMER_URL_PATH_EDIT, ['id' => $item['product_id']]).'">'.$productName.'</a>');
                }
            }
        }
        return $dataSource;
    }

    private function getProductName($productId)
    {
        $product = $this->product->load($productId);
        $name = $product->getName();
        return $name;
    }
}
