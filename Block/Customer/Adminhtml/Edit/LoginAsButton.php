<?php

namespace Flancer32\LoginAs\Block\Customer\Adminhtml\Edit;

use Flancer32\LoginAs\Config as Cfg;
use \Flancer32\LoginAs\Controller\Adminhtml\Redirect\Index as CntrlRedirect;
class LoginAsButton
    extends \Magento\Customer\Block\Adminhtml\Edit\GenericButton
    implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /** @var \Magento\Framework\AuthorizationInterface */
    private $authorization;
    /** @var \Magento\Customer\Api\AccountManagementInterface */
    private $customerAccountManagement;
    /** @var \Flancer32\LoginAs\Helper\Config */
    private $hlpCfg;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Flancer32\LoginAs\Helper\Config $hlpCfg
    ) {
        parent::__construct($context, $registry);
        $this->authorization = $context->getAuthorization();
        $this->customerAccountManagement = $customerAccountManagement;
        $this->hlpCfg = $hlpCfg;
    }

    public function getButtonData()
    {
        $data = [];
        /* check ACL & store configuration */
        $isAllowed = $this->authorization->isAllowed(Cfg::ACL_RULE_LOGIN_AS);
        $isConfigured = $this->hlpCfg->getControlsCustomerFormButton();
        if ($isAllowed && $isConfigured) {
            $customerId = $this->getCustomerId();
            $canModify = $customerId && !$this->customerAccountManagement->isReadonly($this->getCustomerId());
            if ($customerId && $canModify) {
                $data = [
                    'label' => __('Login As'),
                    'class' => 'fl32-login-as',
                    'id' => 'customer-edit-fl32-login-as-button',
                    'on_click' => sprintf("window.open('%s','_blank');", $this->getLoginAsUrl()),
                    'sort_order' => 100,
                ];
            }
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getLoginAsUrl()
    {
        $route = Cfg::ROUTE_NAME_ADMIN_LOGINAS . '/redirect/';
        $id = $this->getCustomerId();
        return $this->getUrl($route, [CntrlRedirect::REQ_PARAM_ID => $id]);
    }
}
