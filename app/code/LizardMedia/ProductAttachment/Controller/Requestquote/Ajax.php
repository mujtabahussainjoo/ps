<?php

   namespace LizardMedia\ProductAttachment\Controller\Requestquote;

   use Magento\Framework\App\Action\Context;

   class Ajax extends \Magento\Framework\App\Action\Action{

       /**
        * @var \Magento\Framework\App\Request\Http
        */
       protected $_request;
       /**
        * @var \Magento\Framework\Mail\Template\TransportBuilder
        */
       protected $_transportBuilder;
       /**
        * @var \Magento\Store\Model\StoreManagerInterface
        */
       protected $_storeManager;

       protected $scopeConfig;

       protected $inlineTranslation;

       public function __construct(
           \Magento\Framework\App\Action\Context $context,
           \Magento\Framework\App\Request\Http $request,
           \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
           \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
           \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
           \Magento\Store\Model\StoreManagerInterface $storeManager
       )
       {
           $this->_request = $request;
           $this->_transportBuilder = $transportBuilder;
           $this->_storeManager = $storeManager;
           $this->scopeConfig = $scopeConfig;
           $this->inlineTranslation = $inlineTranslation;
           parent::__construct($context);
       }

       public function execute()
       {
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ajaaa.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);


           if ($this->getRequest()->getParams()) {
               $parms = $this->getRequest()->getParams();
               try{
                   $data = array();
                   $data['cust_name'] = $parms['name'];
                   $data['their_url'] = $parms['siteurl'];
                   $data['cust_email'] = $parms['email'];
                   $data['details'] = $parms['details'];
                   $data['prodname'] = $parms['productname'];
                   $data['sku'] = $parms['productsku'];
                   $data['our_produrl'] = $parms['producturl'];

                   //$sendEmail = $this->scopeConfig->getValue('trans_email/ident_sales/email');
                   $sendEmailAdmin = "sales@onlypos.com.au";
                   $sendEmailCustomer = $data['cust_email'];
				   
                   $sendName = $this->scopeConfig->getValue('trans_email/ident_sales/name');
                   $sendNameCustomer = $this->scopeConfig->getValue('trans_email/ident_sales/name');

                   $customerSupportEmail = $this->scopeConfig->getValue('trans_email/ident_support/email');
                   $customerSupportName = $this->scopeConfig->getValue('trans_email/ident_support/name');


                   //$logger->info($sendEmail);

                   if($customerSupportEmail == ''){
                       $customerSupportEmail = $sendEmail;
                       $customerSupportName = $sendName;
                   }

                   $senderadmin = ['name' => $data['cust_name'] ,'email' => $data['cust_email']];
				   $sendercustomer = ['name' => "OnlyPOS Team",'email' => "sales@onlypos.com.au"];
                   $postObject = new \Magento\Framework\DataObject();
                   $postObject->setData($data);
                   $this->inlineTranslation->suspend();
                   $store = $this->_storeManager->getStore()->getId();
                   $transport = $this->_transportBuilder->setTemplateIdentifier(18)
                       ->setTemplateOptions(
                           [
                               'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                               'store' => 1
                           ]
                       )
                       ->setTemplateVars(
                           [
                               'data' => $postObject,
                           ]
                       )
                       ->setFrom($senderadmin)
                       ->addTo($sendEmailAdmin, $sendName)
                       ->getTransport();
                   $transport->sendMessage();
				   
				    $this->inlineTranslation->suspend();
                   $store = $this->_storeManager->getStore()->getId();
                   $transport1 = $this->_transportBuilder->setTemplateIdentifier(19)
                       ->setTemplateOptions(
                           [
                               'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                               'store' => 1
                           ]
                       )
                       ->setTemplateVars(
                           [
                               'data' => $postObject,
                           ]
                       )
                       ->setFrom($sendercustomer)
                       ->addTo($sendEmailCustomer, $sendName)
                       ->getTransport();
                   $transport1->sendMessage();
                   $this->inlineTranslation->resume();
                   $status['status'] = "success";
               }catch (\Exception $e) {
                   $status['status'] = "error";
                   $status['message'] = $e->getMessage();
                   $this->messageManager->addError(__($e->getMessage()));
               }

           }else{
               $status['status'] = "error";
               $status['message'] = "Some data miss match";
           }
           echo json_encode($status);
       }
   }