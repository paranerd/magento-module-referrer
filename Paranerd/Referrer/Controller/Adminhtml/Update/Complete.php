<?php
namespace Paranerd\Referrer\Controller\Adminhtml\Update;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Complete extends Action
{
    const ADMIN_RESOURCE = 'Paranerd_Referrer::customer_update';

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
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Paranerd_Referrer::customer_update');
        $resultPage->addBreadcrumb(__('Update'), __('Update'));
        $resultPage->addBreadcrumb(__('Update Customer Levels'), __('Update Customer Levels'));
        $resultPage->getConfig()->getTitle()->prepend(__('Update Complete'));

        return $resultPage;
    }
}