<?php

namespace LizardMedia\ProductAttachment\Controller\Requestquote;

use Magento\Framework\App\Action\Context;

class Enquiry extends \Magento\Framework\App\Action\Action{

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

    public function execute(){

        if ($this->getRequest()->getParams()) {
            $parms = $this->getRequest()->getParams();
            try{
                $data = array();
                $data['email'] = $parms["email"];
                $data['firstname'] = $parms["firstname"];
                $data['lastname'] = $parms["lastname"];
                $data['mobile'] = $parms["telephone"];
                $data['company'] = $parms["company"];
                $data['industry'] = $parms["industry"];
                $data['posrequirement'] = implode(',', $parms["requirement"]);
                $data['description'] = $parms["description"];

                $sendEmail = $this->scopeConfig->getValue('trans_email/ident_general/email');
                $sendName = $this->scopeConfig->getValue('trans_email_ident_general_name');
				
                if($sendName == ''){
                    $sendName = 'OnlyPOS';
                }
				
                $sender = ['name' => $sendName,'email' => $sendEmail];
                $reciever = $sender;
                
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($data);
                $this->inlineTranslation->suspend();
                $store = $this->_storeManager->getStore()->getId();
                $transport = $this->_transportBuilder->setTemplateIdentifier(25)
                            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,'store' => 1])
                            ->setTemplateVars(['data' => $postObject,])
                            ->setFrom($sender)
                            ->addTo($sendEmail,$sendName)//($sendEmail, $sendName)
                           //->addTo('mujtaba.hussain@serole.com','Mujtaba')//($sendEmail, $sendName)
                           ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                $this->messageManager->addSuccessMessage("Request sent successfully");
            }catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
            }

        }else{
            $this->messageManager->addError(__("something went wrong"));
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}