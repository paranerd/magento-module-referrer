<?php

namespace Paranerd\Referrer\Observer;

class OrderRemove implements \Magento\Framework\Event\ObserverInterface
{
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Referrer\Model\PendingBonusFactory $db
	) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->bonusFactory = $db;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		//$data = $observer->getData('data');
		$order = $observer->getEvent()->getOrder();

		$this->bonusFactory->create()->getCollection()
			->addFieldToFilter('order_id', $order->getId())
			->walk('delete');
	}
}