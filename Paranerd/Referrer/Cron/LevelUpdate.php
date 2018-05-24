<?php

namespace Paranerd\Referrer\Cron;

class LevelUpdate
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

	/**
	* @var \Magento\Customer\Model\Customer
	*/
	 protected $customer;

	 protected $orderCollectionFactory;

	 protected $customerRepositoryInterface;

	 protected $sales_targets;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Magento\Customer\Model\Customer $customer,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
		$this->orderCollectionFactory = $orderCollectionFactory;
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->customer = $customer;
		$this->currentCustomer = $currentCustomer;
    }

	/**
	 * Called once a month
	 * Updates customer-levels
	 */
	public function execute() {
		// Update customers
		$customers = $this->customerRepositoryInterface->getAll();

		foreach ($customers as $customer) {
			$customer->updateLevel();
		}
	}
}
