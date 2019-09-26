<?php
namespace Serole\CallForPrice\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Serole\CallForPrice\Model\ResourceModel\CallForPrice\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Mass action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * Collection Factory
     *
     * @var Vendor\Module\Model\ResourceModel\QuoteRequest\CollectionFactory
     */
    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Filter  $filter,
        CollectionFactory   $collectionFactory
    )
    {
        parent::__construct($context);
        $this->_collectionFactory = $collectionFactory;
        $this->_filter  =   $filter;
    }

    public function execute()
    {
      $collection   =   $this->_filter->getCollection($this->_collectionFactory->create());
      $collectionSize = $collection->getSize();
      foreach ($collection as $item){
          $item->delete();
      }

      $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
      $resultRedirect   =   $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
      $resultRedirect->setUrl($this->_redirect->getRefererUrl());
      return $resultRedirect;
    }

}