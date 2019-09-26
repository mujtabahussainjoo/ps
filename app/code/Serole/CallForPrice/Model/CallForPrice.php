<?php
namespace Serole\CallForPrice\Model;

class CallForPrice extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'serole_callforprice';

	protected $_cacheTag = 'serole_callforprice';

	protected $_eventPrefix = 'serole_callforprice';

	protected function _construct()
	{
		$this->_init('Serole\CallForPrice\Model\ResourceModel\CallForPrice');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}