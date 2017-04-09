<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Controller\Adminhtml\Logged;

use Flancer32\LoginAs\Config as Cfg;
use Magento\Framework\App\ResponseInterface;

class Index
    extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_CUSTOMER_LOGGED_AS;
        $resultPage->setActiveMenu($activeMenu);
        $this->_addBreadcrumb(__('Breadcrumb label'), __('Breadcrumb title'));
        $resultPage->getConfig()->getTitle()->prepend(__('Logged As'));
        return $resultPage;
    }

}