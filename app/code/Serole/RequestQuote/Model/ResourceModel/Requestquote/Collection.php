<?php

namespace Serole\RequestQuote\Model\ResourceModel\Requestquote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\RequestQuote\Model\Requestquote', 'Serole\RequestQuote\Model\ResourceModel\Requestquote');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>