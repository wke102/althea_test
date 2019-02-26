<?php

namespace Karl\Test\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	public function execute()
	{
		try{
			$resultPage = $this->resultPageFactory->create();
			$resultPage->getConfig()->getTitle()->prepend((__('Stock Checker')));	
		}
		catch(Exception $e){
			$this->messageManager->addError($e);	
		}
		
		return $resultPage;
	}

}