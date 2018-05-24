<?php

namespace Paranerd\Referrer\Observer;

class InvoiceSave implements \Magento\Framework\Event\ObserverInterface
{
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Referrer\Model\PendingBonusFactory $db
	) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->bonusFactory = $db;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		return;

		//$data = $observer->getData('data');
		$event = $observer->getEvent();
		$invoice = $event->getInvoice();
		$order = $invoice->getOrder();
		$customer_id = $order->getCustomerId();
		$customer = $this->customerRepositoryInterface->getById($customer_id);

		if ($customer) {
			$this->bonusFactory->create()->setData(array('customer_id' => $customer_id, 'amount' => $order->getBaseSubtotalInclTax()))->save();
		}
	}
}