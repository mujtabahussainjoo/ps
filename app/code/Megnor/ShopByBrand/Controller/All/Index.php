<?php
namespace Megnor\ShopByBrand\Controller\All;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageConfig;
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Page\Config $pageConfig
    ) {
		$this->_pageConfig = $pageConfig;
        return parent::__construct($context);		
      }
    public function execute()
    {
        $this->_view->loadLayout();
		$this->_pageConfig->getTitle()->set("Top Brands of POS Hardware: Compatible with your POS System Software | OnlyPOS");
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
    
}