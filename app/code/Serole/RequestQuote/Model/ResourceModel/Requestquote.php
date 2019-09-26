<?php
namespace Serole\RequestQuote\Model\ResourceModel;

class Requestquote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('request_quote', 'id');
    }
}
?>