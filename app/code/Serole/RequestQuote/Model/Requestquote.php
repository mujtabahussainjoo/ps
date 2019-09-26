<?php
namespace Serole\RequestQuote\Model;

class Requestquote extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\RequestQuote\Model\ResourceModel\Requestquote');
    }
}
?>