<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Product Tabs for Magento 2
 */

namespace Amasty\CustomTabs\Controller\Adminhtml\Tabs;

use Amasty\CustomTabs\Controller\Adminhtml\Tabs;
use Amasty\CustomTabs\Model\Source\Type;
use Amasty\CustomTabs\Model\Tabs\Repository;
use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Amasty\CustomTabs\Api\Data\TabsInterface;

class Duplicate extends Tabs
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Repository $repository,
        LoggerInterface $logger,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($tabId = $this->getRequest()->getParam(TabsInterface::TAB_ID)) {
            try {
                $tab = $this->repository->getById((int)$tabId);
                if ($tab->getType() == Type::CUSTOM) {
                    $newTab = $this->repository->duplicate($tab);
                    $this->messageManager->addSuccessMessage(__('You have duplicated the tab.'));
                    $this->_redirect('*/*/edit', [TabsInterface::TAB_ID => $newTab->getTabId()]);
                    return;
                } else {
                    $this->messageManager->addErrorMessage(__('You can\'t duplicated default tab.'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t duplicate item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }
        $this->_redirect('*/*/');
    }
}
