<?php

namespace Paranerd\Referrer\Observer;

class OrderPlace implements \Magento\Framework\Event\ObserverInterface
{
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Referrer\Model\PendingBonusFactory $db,
		\Magento\Sales\Model\Order $order
	) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->bonusFactory = $db;
		$this->order = $order;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		//$data = $observer->getData('data');
		$orderId = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderId);

		$this->bonusFactory->create()
			->setData(array('customer_id' => $order->getCustomerId(), 'order_id' => $order->getId(), 'amount' => $order->getBaseSubtotalInclTax()))
			->save();
	}
}