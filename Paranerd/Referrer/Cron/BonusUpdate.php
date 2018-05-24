<?php

namespace Paranerd\Referrer\Cron;

class BonusUpdate
{
	 protected $customerRepositoryInterface;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Paranerd\Referrer\Model\PendingBonusFactory $db,
        array $data = []
    ) {
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->bonusFactory = $db;
    }

	/**
	 * Called once a day
	 * Grabs all pending bonuses within cancellation-range
	 * Distributes bonus accordingly and removes pending bonus
	 */
	public function execute() {
		$storno_frist = "4 weeks";
		$range = array('from' => date('Y-m-d', strtotime('-' . $storno_frist)));
		$data = $this->bonusFactory->create()->getCollection()
			->addFieldToFilter('created_at', $range);

		foreach ($data as $d ) {
			$customer = $this->customerRepositoryInterface->getById($d->getCustomerId());
			if ($customer) {
				$customer->distributeBonus($d->getAmount());
				// Remove this pending bonus
				$data = $this->bonusFactory->create()->getCollection()
					->addFieldToFilter('id', $d->getId())
					->addFieldToFilter('created_at', $range)
					->walk('delete');
			}
		}
	}
}
