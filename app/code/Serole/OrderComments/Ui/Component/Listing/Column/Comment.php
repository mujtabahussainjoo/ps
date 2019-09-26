<?php
namespace Serole\OrderComments\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class Comment extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;

    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, OrderRepositoryInterface $orderRepository, SearchCriteriaBuilder $criteria, array $components = [], array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $com = $user = '';
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name'); 
                $order  = $this->_orderRepository->get($item["entity_id"]);
                $comments = $order->getStatusHistoryCollection();
                foreach ($comments as $comment) {
                    $com = $comment->getComment();
                    if($comment->getAdminUser() != '') {
                        $user = ' ('.$comment->getAdminUser().')';
                    }else{
                        $user = '(Automatic)';
                    }
                    break;
                }
                $item[$this->getData('name')] = $com.$user;
                $com = $user = '';
            }
        }   
        return $dataSource;
    }
}