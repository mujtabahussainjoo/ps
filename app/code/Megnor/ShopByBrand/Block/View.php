<?php
namespace Megnor\ShopByBrand\Block;

class View extends \Magento\Catalog\Block\Product\AbstractProduct implements
    \Magento\Framework\DataObject\IdentityInterface
{
	
	/**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;
    
	/**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    
    /**
     * Image helper
     *
     * @var Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
     /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    protected $_brandFactory;
	
	protected $_pageConfig;
	
	protected $_storeManager;


	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Megnor\ShopByBrand\Model\BrandFactory $brandFactory,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->_imageHelper = $context->getImageHelper();
        $this->_brandFactory = $brandFactory;
        $this->_cartHelper = $context->getCartHelper();
		$this->_pageConfig = $pageConfig;
		$this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $data
        );
    }
	 public function getAddToCartUrl($product, $additional = [])
    {
			return $this->_cartHelper->getAddUrl($product, $additional);
    }
    
	
	 public function getImageMediaPath(){
    	return $this->getUrl('pub/media',['_secure' => $this->getRequest()->isSecure()]);
    }
	
	public function getExtraTags()
	{
		 $storeUrl = $this->_storeManager->getStore()->getBaseUrl();
		 $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$id = $this->getRequest()->getParam('id');
		
		$extraMeta = '';
        if ($id) {
        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$model = $objectManager->create('Megnor\ShopByBrand\Model\Items');
			$brand = $model->load($id);
			$brandData = $brand->getData();
			
			$mediaUrlBrand = $mediaUrl.$brandData['logo'];
			$brandUrl = $storeUrl."brand/".$brandData['url_key'];
			$extraMeta = $extraMeta.'<meta name="twitter:card" content="'.$mediaUrlBrand.'"/>';
			$extraMeta = $extraMeta.'<meta name="twitter:site" content="@onlypos"/>';
			$extraMeta = $extraMeta.'<meta name="twitter:creator" content="@onlypos"/>';
			$extraMeta = $extraMeta.'<meta name="twitter:url" content="'.$brandUrl.'"/>';
			$extraMeta = $extraMeta.'<meta name="twitter:description" content="'.$brandData['metadescription'].'"/>';
			$extraMeta = $extraMeta.'<meta name="twitter:image" content="'.$mediaUrlBrand.'">';
			$extraMeta = $extraMeta.'<meta property="og:type" content="product.group"/>';
			$extraMeta = $extraMeta.'<meta property="og:url" content="'.$brandUrl.'"/>';
			$extraMeta = $extraMeta.'<meta property="og:title" content="'.$brandData['opengraph_title'].'"/>';
			$extraMeta = $extraMeta.'<meta property="og:description" content="'.$brandData['metadescription'].'"/>';
			$extraMeta = $extraMeta.'<meta property="og:image" content="'.$mediaUrlBrand.'"/>';
			$extraMeta = $extraMeta.'<meta property="og:site_name" content="OnlyPOS"/>';
			
		}
		return $extraMeta;
	
	}
	
    public function getBrand(){
		$id = $this->getRequest()->getParam('id');
        if ($id) {
        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$model = $objectManager->create('Megnor\ShopByBrand\Model\Items');
			$brand = $model->load($id);
			$brandData = $brand->getData();
			
			if(isset($brandData['metatitle']))
				$this->_pageConfig->getTitle()->set($brandData['metatitle']);

			if(isset($brandData['metakeywords']))        
				$this->_pageConfig->setKeywords($brandData['metakeywords']);

			if(isset($brandData['metadescription']))         
				$this->_pageConfig->setDescription($brandData['metadescription']);
					
			return $brand;
		}
		return false;
    }


    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function _addBreadcrumbs()
    {
       $brand = $this->getBrand();
      $brands = $this-> getBrands();
       $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

       if($breadcrumbsBlock){

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'brands',
                [
                    'label' => __('Brands'),
                    'title' => __('Go to Brand Page'),
                    'link' =>  $this->_storeManager->getStore()->getUrl('brand/all')
                ]
            );
            $breadcrumbsBlock->addCrumb('brand', [
                'label' => $brand->getName(),
                'title' => $brand->getName()
            ]);
        }
    }

    public function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        return parent::_prepareLayout();
    }
    
    public function getProductCollection()
    {
    	$brand = $this->getBrand();
    	$collection = $this->_productCollectionFactory->create();
    	$collection->addAttributeToSelect('*');

		$collection->addAttributeToSelect('name');
    	$collection->addStoreFilter()->addAttributeToFilter('manufacturer' , $brand->getAttributeId());

    	return $collection;
    }
    
    public function imageHelperObj(){
        return $this->_imageHelper;
    }
	
	
     /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;
            /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }



	 /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }
     public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}