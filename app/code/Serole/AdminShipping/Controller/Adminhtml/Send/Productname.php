<?php
namespace Serole\AdminShipping\Controller\Adminhtml\Send;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Store\Model\ScopeInterface;

class Productname extends \Magento\Backend\App\Action
{
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\Data\OrderInterface $order,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_order = $order;
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $postData = $this->getRequest()->getParams();
        $orderId = $postData['orderId'];
        $itemIds = $postData['itemId'];

        $order = $this->_order->load($orderId);
        $productData = '';
        if($itemIds == 'err')
        {
            $this->messageManager->addError( __('Please select Item first!') );
            exit;
        }
        echo '<h5 style="margin-bottom:0;">Product Information:</h5>';
        foreach ($itemIds as $itemId) {
            foreach ($order->getAllItems() as $orderData) {
                if($orderData->getItemId() == $itemId) {
                    echo '<p style="font-size:.7em;">'.round($orderData->getQtyOrdered()).' X '.$orderData->getName().'</p>';
                }
            }
        }
        echo '<h5 style="margin-bottom:0;">Shipping Information:</h5>';
        $shippingAddressObj = $order->getShippingAddress();
        $shippingAddressArray = $shippingAddressObj->getData();
        $firstname = $shippingAddressArray['firstname'];
        $lastname = $shippingAddressArray['lastname'];
        $telephone = $shippingAddressArray['telephone'];
        $street = $shippingAddressArray['street'];
        $city = $shippingAddressArray['city'];
        $region = $shippingAddressArray['region'];
        $postcode = $shippingAddressArray['postcode'];

        $shippingDetail = $firstname.' '.$lastname.'<br/>'.$street.'<br/>'.$city.', '.$region.', '.$postcode.'<br/>'.$telephone; 
        echo '<p style="font-size:.7em;">'.$shippingDetail.'</p>';
        exit;
    }
}
