<?php

namespace Paranerd\Referrer\Model\Customer\Data;

use \Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Customer
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Customer extends \Magento\Customer\Model\Data\Customer implements \Magento\Customer\Api\Data\CustomerInterface
{
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $attributeValueFactory
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadataService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $attributeValueFactory,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
		\Magento\Framework\Event\Manager $eventManager,
		\Paranerd\Referrer\Model\PendingBonusFactory $db,
        $data = []
    ) {
		include (__DIR__) . '/../../../sales_targets.php';
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerFactory = $customerFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->eventManager = $eventManager;
		$this->bonusFactory = $db;
        $this->sales_targets = $sales_targets;

        parent::__construct(
			$extensionFactory,
			$attributeValueFactory,
			$metadataService,
			$data
		);
	}

	/**
     * Get Bonus
	 *
     * @return int
     */
	public function getBonus() {
		$bonus = $this->getCustomAttribute('bonus')->getValue();
		return ($bonus) ? $bonus : 0;
	}

	/**
     * Get Level
	 *
     * @return int|null
     */
	public function getLevel() {
		return $this->getCustomAttribute('level')->getValue();
	}

	/**
     * Get Referrer
     *
     * @return string|null
     */
	public function getReferrer() {
		return $this->getCustomAttribute('referrer')->getValue();
	}

	/**
     * Get Custom ID
     *
     * @return string|null
     */
	public function getCustomId() {
		return $this->getCustomAttribute('custom_id')->getValue();
	}

	private function setReferDate($timestamp) {
		$this->setCustomAttribute('last_referred', $timestamp);
	}

	/**
	 * Returns the sales the customer did himself, without counting child-sales
	 * @return int
	 */
	public function ownSales($range = null) {
		$sum = 0;
		$orders = $this->orderCollectionFactory->create()->addFieldToFilter('customer_id', $this->getId());

		if ($range) {
			$orders->addFieldToFilter('created_at', $range);
		}

		foreach ($orders as $order) {
			$sum += $order->getBaseSubtotalInclTax();
		}

		return $sum;
	}

	/**
	 * Returns the sales the children
	 * May also include customers own shares
	 * @return array
	 */
	public function childSales($only_usable = false, $sum = false, $include_self = false, $range = null) {
		$children = $this->promoChildren();
		$level = $this->getLevel();
		$sales = ($include_self) ? array($this->ownSales($range)) : array(0);

		foreach ($children as $child) {
			$child_sales = $child->childSales(true, true, true, $range);
			$usable = ($only_usable) ? $this->usableSales($child_sales) : $child_sales[0];

			if ($sum) {
				$sales[0] += $usable;
			}
			else {
				array_push($sales, $usable);
			}
		}

		return $sales;
	}

	/**
	 * Returns a list of customer-entities that are directly referred by the customer
	 *
	 * @return $this
	 */
	private function promoChildren() {
		$searchCriteria = $this->searchCriteriaBuilder->addFilter('referrer', $this->getCustomId(), 'eq')->create();
		$customersList = $this->customerRepositoryInterface->getList($searchCriteria);
		return $customersList->getItems();
	}

	/**
	 * Checks if the customer exceeded its sales-target and raises his level accordingly
	 *
	 * @return int|null
	 */
	public function updateLevel() {
		$level = $this->getLevel();
		$level_backup = $level;
		$range = null;// $this->calculateSemester();
		$own_sales = $this->ownSales($range);
		$child_sales = $this->childSales($range);

		while ($level < sizeof($this->sales_targets) && $own_sales + $this->usableSales($child_sales, $level) > $this->sales_targets[$level]['sales']) {
			$level++;
		}

		if ($level_backup != $level) {
			$this->raiseLevel($level);
		}

		return $level;
	}

	/**
	* Each direct child-branch can only provide 50% of the required sales for level-upgrade
	* Meaning 2 child-branches with each 50% of the required sales make for an upgrade
	* As well as 1 child with 75% (that gets cut to 50%) of the required sales
	* plus another (or the owner) with another 50%
	*
	* @return int
	*/
	private function usableSales($sales, $level = null) {
		$level = ($level) ? $level : $this->getLevel();
		$sales_target = ($level < sizeof($this->sales_targets) - 1) ? $this->sales_targets[$level]['sales'] : $this->sales_targets[sizeof($this->sales_targets) - 1]['sales'];
		$usable_sales = 0;

		for ($i = 0; $i < sizeof($sales); $i++) {
			$usable_sales += ($sales[$i] > $sales_target / 2) ? $sales_target / 2 : $sales[$i];
		}

		return $usable_sales;
	}

	/**
	* Raises level for customer and his parents
	*
	* @return int|null
	*/
	private function raiseLevel($level) {
		$referrer = $this->customerRepositoryInterface->getByCustomId($this->getReferrer());

		// Change > to == to change parents on equal level
		while ($referrer && $level > $referrer->getLevel() && $grand_parent = $referrer->getReferrer()) {
			$referrer = $this->customerRepositoryInterface->getByCustomId($grand_parent);
		}

		$this->setCustomAttribute('level', $level);

		if ($referrer) {
			$this->setCustomAttribute('referrer', $referrer->getCustomId());
		}
		$this->customerRepositoryInterface->save($this);

		return $level;
	}

	/**
	* Gets called by the "BonusUpdate"-Cron-Job
	*
	* @return void
	*/
	public function distributeBonus($amount = 0) {
		$level = $this->getLevel();
		$parents = $this->promoParents();

		// Distribute Bonus to self
		$self_bonus = $this->getBonus() + $amount * ($this->sales_targets[$level]['bonus'] / 100);
		$this->setCustomAttribute('bonus', $self_bonus);

		// Distribute Bonus to parents
		foreach ($parents as $parent) {
			$bonus = $parent->getBonus();
			$parent_level = $parent->getLevel();
			if ($parent_level - $level > 0) {
				$bonus += $amount * ($this->sales_targets[$parent_level]['bonus'] - $this->sales_targets[$level]['bonus']) / 100;
				//$bonus += $amount * ($parent_level - $level) / 100;
			}
			$level = $parent_level;

			$parent->setCustomAttribute('bonus', $bonus);
			$parent->updateLevel();
			$parent->customerRepositoryInterface->save($parent);
		}

		$this->updateLevel();
		$this->customerRepositoryInterface->save($this);
	}

	/**
	* Add order to pending_bonus table
	*
	* @return void
	*/

	public function distributePendingBonus($order_id, $amount) {
		$this->bonusFactory->create()->setData(array('customer_id' => $this->getId(), 'order_id' => $order_id, 'amount' => $amount))->save();
	}

	/**
	* Get customer's parents
	*
	* @return array
	*/
	private function promoParents() {
		$current = $this;
		$parents = array();

		while ($current->getReferrer()) {
			$current = $this->customerRepositoryInterface->getByCustomId($current->getReferrer());
			array_push($parents, $current);
		}

		return $parents;
	}

	/**
	* Get the semester to calculate sales for
	*
	* @return array
	*/
	private function calculateSemester() {
		$midyear = gmmktime(0, 0, 0, 7, 1, date('Y'));

		$from = (time() >= $midyear) ? date('Y') . '-01-01' : (date('Y') - 1) . '-07-01';
		$to = (time() >= $midyear) ? date('Y') . '-06-30' : (date('Y') - 1) . '-12-31';

		return array('from' => $from, 'to' => $to);
	}
}