<?php

namespace Paranerd\Referrer\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
		Context $context,
		PageFactory $resultPageFactory
		) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
		$this->_view->loadLayout();

		/** @var \Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Promotion'));

		$block = $resultPage->getLayout()->getBlock('customer.account.link.back');
		if ($block) {
			$block->setRefererUrl($this->_redirect->getRefererUrl());
			$block->setActive('customer/account/edit');
		}
		return $resultPage;
    }
}