<?php
namespace Serole\AdminShipping\Controller\Adminhtml\Send;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Store\Model\ScopeInterface;

class Email extends \Magento\Backend\App\Action
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
        $sender['email'] = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
        $sender['name'] = $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE);

        $postData = $this->getRequest()->getParams();
        $supplierName = $postData['name'];
        $to = $postData['email'];
        $cc = preg_split ("/\,/", $postData['cc']);
        $comments = $postData['comments'];
        $signature = $postData['signature'];
        $orderId = $postData['orderId'];
        $itemIds = $postData['itemId'];

        $order = $this->_order->load($orderId);
        $productData = '';
        foreach ($itemIds as $itemId) {
            foreach ($order->getAllItems() as $orderData) {
                if($orderData->getItemId() == $itemId) {
                    $sku = $orderData->getSku();
                    $name = $orderData->getName();
                    $qty = $orderData->getQtyOrdered();
                    $productData = $productData.'<tr><td>'.round($qty).'</td><td>'.$name.'</td><td>'.$sku.'</td></tr>';
                }
            }
        }               
            
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

        $templateVars = array(
            'name' => $supplierName,
            'comments' => $comments,
            'signature' => $signature,
            'productData' => $productData,
            'orderid' => $order->getIncrementId(),
            'shippingDetail' => $shippingDetail
        );

        $this->inlineTranslation->suspend();
        $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier('serole_adminshipping')
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
            ->addTo($to)
            ->addCc($cc)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
        $this->messageManager->addSuccess( __('Email sended!') );
    }
}
