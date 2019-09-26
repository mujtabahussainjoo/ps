<?php
namespace Serole\CallForPrice\Model\ResourceModel\CallForPrice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'serole_callforprice_collection';
	protected $_eventObject = 'callforprice_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Serole\CallForPrice\Model\CallForPrice', 'Serole\CallForPrice\Model\ResourceModel\CallForPrice');
	}

}