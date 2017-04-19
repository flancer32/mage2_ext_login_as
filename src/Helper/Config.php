<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Helper;

/**
 * Helper to get store configuration parameters related to the module.
 *
 * (see ./src/etc/adminhtml/system.xml)
 */
class Config
{

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Permission to display "Login As" button in customer details (adminhtml).
     *
     * @return bool
     */
    public function getControlsCustomerFormButton()
    {
        $result = $this->scopeConfig->getValue('fl32_loginas/controls/customer_form_button');
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        return $result;
    }

    /**
     * Permission to display "Login As" action in customers grid (adminhtml).
     *
     * @return bool
     */
    public function getControlsCustomersGridAction()
    {
        $result = $this->scopeConfig->getValue('fl32_loginas/controls/customers_grid_action');
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        return $result;
    }

    /**
     * Permission to display "Login As" action in sales orders grid (adminhtml).
     *
     * @return bool
     */
    public function getControlsSalesOrdersGridAction()
    {
        $result = $this->scopeConfig->getValue('fl32_loginas/controls/orders_grid_action');
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        return $result;
    }

}