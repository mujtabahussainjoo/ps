<?php

namespace Serole\RequestQuote\Controller\Requestquote;

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

    public function requestQuote()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Serole\RequestQuote\Model\Requestquote');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }
			$requirement = implode(',', $data["requirement"]);
            $model->setData($data);
			$model->setData('requrement',$requirement);
            try {
                $model->save();
				$sendEmail = $this->scopeConfig->getValue('trans_email/ident_general/email');
                $sendName = $this->scopeConfig->getValue('trans_email_ident_general_name');
                if($sendName == ''){
                    $sendName = 'OnlyPOS';
                }
                $sender = ['name' => $sendName,'email' => $sendEmail];
                $reciever = $sender;
				$data["requirement"] = implode(',', $data["requirement"]);
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($data);
                $postObject->setData('requrement',$requirement);
				$templateVars = [
					"data"          => $postObject,
					"requirement" => $data["requirement"]
				];
                $this->inlineTranslation->suspend();
                $store = $this->_storeManager->getStore()->getId();
                $transport = $this->_transportBuilder->setTemplateIdentifier(25)
                            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,'store' => 1])
                            //->setTemplateVars(['data' => $postObject,])
                            ->setTemplateVars($templateVars)
                            ->setFrom($sender)
                            ->addTo($sendEmail,$sendName)//($sendEmail, $sendName)
                           //->addTo('mujtaba.hussain@serole.com','Mujtaba')//($sendEmail, $sendName)
                           ->getTransport();
                $transport->sendMessage();
                $this->messageManager->addSuccess(__('The Requestquote has been saved.'));
               // return $resultRedirect->setPath('*/*/');
			   $this->_redirect($this->_redirect->getRefererUrl());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Requestquote.'));
            }
			$this->_redirect($this->_redirect->getRefererUrl());
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
    public function execute(){

        $this->requestQuote(); 
    }
}