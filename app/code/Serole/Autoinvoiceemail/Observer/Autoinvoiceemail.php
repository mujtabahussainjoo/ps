<?php
namespace Serole\Autoinvoiceemail\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use \Magento\Framework\Exception\LocalizedException;
use \Psr\Log\LoggerInterface;

class Autoinvoiceemail implements \Magento\Framework\Event\ObserverInterface
{
	 /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderModel;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;
	
	protected $_objectManager;

    /**
     * Logger
     * @var LoggerInterface
     */
    protected $logger;
	
	public function __construct(
        \Magento\Sales\Model\OrderFactory $orderModel,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
         LoggerInterface $logger
    )
    {
        $this->orderModel = $orderModel;
        $this->invoiceSender = $invoiceSender;
        $this->logger = $logger;
    }
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	  {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/invoice-email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
		
		$logger->info("Invoice Email");
		 // get the corresponding order & invoice
        $invoice = $observer->getEvent()->getInvoice();
		$invoiceId = $invoice->getId();
		$logger->info("Invoice Id:".$invoice->getId());		
		$invoice_id = $invoice->getData('entity_id');
		$logger->info("invoice_entity Id:".$invoice_id);
        $order = $invoice->getOrder();
        $logger->info("order Id:".$order->getId());

        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		 if (!$invoiceId) {
             $logger->info("Invoice Id Issue");
        }
		
        $invoiceData = $this->_objectManager->create(\Magento\Sales\Api\InvoiceRepositoryInterface::class)->get($invoiceId);
        if (!$invoiceData) {
            $logger->info("Invoice Issue");
        }

       

        if (!$order->getId()) {
            throw new LocalizedException(__('The order no longer exists.'));
        }

        // send invoice email only for PayPal Plus and Amazon Pay AND if order status is "new" or "processing"
        
                    try {
						 $this->_objectManager->create(
							\Magento\Sales\Api\InvoiceManagementInterface::class
						)->notify($invoiceData->getEntityId());
						$logger->info("Invoice Sent:");
                    } catch (\Exception $e) {
						$logger->info("Error:".$e->getMessage());
                        $this->logger->error($e->getMessage());
                    }
                    // add order comment
                    $order->addStatusHistoryComment(
                        'Invoice Email Sent',
                        true
                    )->save();

                
	  }
	  
	   /**
     * @param $order
     * @throws LocalizedException
     */
    protected function checkOrder($order,$logger)
    {
		return true;
		$logger->info("checkOrder In:");
        if (!$order->canInvoice()
        ) {
			$logger->info("can not Invoice :");
            throw new LocalizedException(
                __('The order does not allow an invoice to be created.')
            );
        }
    }

}