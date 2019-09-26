<?php
namespace Serole\AutoInvoice\Observer;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
class Autoinvoice implements \Magento\Framework\Event\ObserverInterface
{
    protected $_invoiceService;
    protected $_transactionFactory;

    public function __construct(
	  InvoiceSender $invoiceSender,
      \Magento\Sales\Model\Service\InvoiceService $invoiceService,
      \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
       $this->_invoiceService = $invoiceService;
       $this->_transactionFactory = $transactionFactory;
	   $this->_invoiceSender = $invoiceSender;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$invoice = $observer->getEvent()->getInvoice();
		$invoice_id = $invoice->getData('entity_id');
		//if (!$invoice->getEmailSent()) {
			try {
					$this->_invoiceSender->send($invoice);
				} catch (\Exception $e) {
					// Do something if failed to send                          
				}
		//}
	}
	
}