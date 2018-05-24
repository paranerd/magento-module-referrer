<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paranerd\Referrer\Block\Account\Dashboard;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Dashboard Customer Info
 */
class Referrer extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Customer\Helper\View */
    protected $_helperView;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
     protected $customer;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Helper\View $helperView
     * @param array $data
     */
    public function __construct(
		\Magento\Customer\Model\Customer $customer,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Helper\View $helperView,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
		$this->customer = $customer;
        $this->currentCustomer = $currentCustomer;
        $this->_helperView = $helperView;
        $this->orderCollectionFactory = $orderCollectionFactory;

		parent::__construct($context, $data);
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer() {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get the referrer
     *
     * @return string referrer
     */
    public function get_custom_id($id = null) {
		$customer = $this->getCustomer();
		return ($customer) ? $customer->getCustomId() : '';
	}

	public function get_level() {
		$this->getCustomer()->updateLevel();
		return $this->getCustomer()->getLevel();
	}

  public function getChildSales() {
    return $this->getCustomer()->childSales(false, true)[0];
  }

    /**
     * @return string
     */
    protected function _toHtml() {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : 'You are not logged in';
    }
}
