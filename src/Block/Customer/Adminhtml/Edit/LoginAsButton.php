<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Flancer32\LoginAs\Block\Customer\Adminhtml\Edit;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use \Magento\Customer\Block\Adminhtml\Edit\GenericButton;

class LoginAsButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        AccountManagementInterface $customerAccountManagement
    )
    {
        parent::__construct($context, $registry);
        $this->customerAccountManagement = $customerAccountManagement;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $canModify = $customerId && !$this->customerAccountManagement->isReadonly($this->getCustomerId());
        $data = [];
        if ($customerId && $canModify) {
            $data = [
                'label' => __('Login As'),
                'class' => 'fl32-login-as',
                'id' => 'customer-edit-fl32-login-as-button',
                'on_click' => sprintf("location.href = '%s';", $this->getLoginAsUrl()),
                'sort_order' => 100,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getLoginAsUrl()
    {
        return $this->getUrl('*/*/fl32LoginAs', ['id' => $this->getCustomerId()]);
    }
}
