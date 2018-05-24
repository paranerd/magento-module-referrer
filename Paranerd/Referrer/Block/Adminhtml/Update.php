<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Paranerd\Referrer\Block\Adminhtml;

date_default_timezone_set("UTC");

class Update extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'customer_update/update_start.phtml';

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

     protected $orderCollectionFactory;

     protected $customerRepositoryInterface;

     protected $sales_targets;

     protected $redirectFactory;

     protected $response;

     protected $redirect;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Helper\View $helperView
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
		\Magento\Framework\App\Response\Http $response,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Magento\Customer\Model\Customer $customer,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Helper\View $helperView,
        array $data = []
    ) {
		$this->redirect = $redirect;
		$this->response = $response;
		$this->redirectFactory = $redirectFactory;
		$this->orderCollectionFactory = $orderCollectionFactory;
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->customer = $customer;
    $this->currentCustomer = $currentCustomer;
    $this->_helperView = $helperView;

    parent::__construct($context, $data);

    $this->init_update();
    }

    /**
     * @return void
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

	public function init_update() {
		$customers = $this->customerRepositoryInterface->getAll();

		foreach ($customers as $customer) {
			$customer->updateLevel();
		}

		$this->redirect();
	}

	public function redirect() {
		$this->redirect->redirect($this->response, 'customer/update/complete');
	}
}
