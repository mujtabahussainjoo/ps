<?php
namespace Serole\CallForPrice\Controller\Post;

use Magento\Store\Model\ScopeInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

	protected $_callforpriceFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Serole\CallForPrice\Model\CallForPriceFactory $callforpriceFactory,
		 \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $productData
	)
	{
		$this->_pageFactory = $pageFactory;
		$this->_callforpriceFactory = $callforpriceFactory;
		$this->storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->productData = $productData;
		return parent::__construct($context);
	}

	public function execute()
	{
		$postData = $this->getRequest()->getParams();
		$post = $this->_callforpriceFactory->create();
		$post->addData($postData);
		$saveData = $post->save();
		if($saveData){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$sender['email'] = $postData['email'];
	        $sender['name'] = $postData['name'];
	        $to = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);

	        $product = $this->productData->create()->load($postData['product_id']);
            $imageHelper = $objectManager->get('Magento\Catalog\Helper\Image');
            $image_url = $imageHelper->init($product, 'cart_page_product_thumbnail')->getUrl();

            $productName = "<a href='".$product->getProductUrl()."'>".$product->getName()."</a>";
            $image = "<a href='".$product->getProductUrl()."'><img src='".$image_url."'></a>";

            $templateVars = array(
	            'name' => $postData['name'],
	            'email' => $postData['email'],
	            'mobile_number' => $postData['mobile_number'],
	            'note' => $postData['note'],
	            'product_image' => $image,
	            'product_name' => $productName
	        );

	    	$this->inlineTranslation->suspend();
	        $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
	        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
	        $transport = $this->_transportBuilder
	        ->setTemplateIdentifier('serole_callforprice_notify')
	        ->setTemplateOptions($templateOptions)
	        ->setTemplateVars($templateVars)
	        ->setFrom($sender)
	        ->addTo($to)
	        ->getTransport();
	        $transport->sendMessage();
	        $this->inlineTranslation->resume();

			$this->messageManager->addSuccess( __('Your query submited successfully. Our Sales person will contact you soon!') );
        }
		exit();
		return $this->_pageFactory->create();
	}
}