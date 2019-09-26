<?php

namespace Aune\AutoInvoice\Model;

use Magento\Framework\DataObject;
use Aune\AutoInvoice\Api\Data\InvoiceProcessItemInterface;

class InvoiceProcessItem extends DataObject implements InvoiceProcessItemInterface
{
    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return $this->getData(self::KEY_ORDER);
    }
    
    /**
     * @inheritdoc
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $this->setData(self::KEY_ORDER, $order);
    }
    
    /**
     * @inheritdoc
     */
    public function getDestinationStatus()
    {
        return $this->getData(self::KEY_DESTINATION_STATUS);
    }
    
    /**
     * @inheritdoc
     */
    public function setDestinationStatus(string $status)
    {
        return $this->setData(self::KEY_DESTINATION_STATUS, $status);
    }
}
