<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users\Check\Roles;

use \Flancer32\LoginAs\Config as Cfg;

class Acl
{
    const CTX_ROLES_MAP = 'roles';
    /** @var \Magento\Framework\Acl\Builder */
    protected $aclBuilder;
    /** @var \Magento\Framework\Authorization\PolicyInterface */
    protected $aclPolicy;
    /** @var \Magento\Framework\AuthorizationInterface */
    protected $authorization;
    /** @var \Magento\Authorization\Model\Role */
    protected $modRole;
    protected $modRule;
    /** @var \Magento\Framework\Authorization\RoleLocatorInterface */
    protected $roleLocator;

    public function __construct(
        \Magento\Framework\Acl\Builder $aclBuilder,
        \Magento\Framework\Authorization\PolicyInterface $aclPolicy,
        \Magento\Authorization\Model\Role $modRole,
        \Magento\Authorization\Model\Rules $modRule
    ) {
        $this->aclBuilder = $aclBuilder;
        $this->aclPolicy = $aclPolicy;
        $this->modRole = $modRole;
        $this->modRule = $modRule;
    }

    public function exec(\Flancer32\Lib\Data $ctx)
    {
        $roles = $ctx->get(self::CTX_ROLES_MAP);

        $acl = $this->aclBuilder->getAcl();
        $all = $acl->getResources();
        foreach ($roles as $code => $id) {
            /* I know, 'load()' is deprecated but what to use instead? */
            $this->modRole->load($id);
            $allowed = $this->getAllowed($code);
            $this->aclPolicy->isAllowed($id, "Flancer32_LoginAs::dome_resource");
        }
    }

    protected function getAllowed($roleCode)
    {
        switch ($roleCode) {
            case Cfg::ACL_ROLE_FULL:
                $result = $this->getAllowedForFull();
                break;
            case Cfg::ACL_ROLE_LOGIN:
                $result = $this->getAllowedForLoginAs();
                break;
            case Cfg::ACL_ROLE_LOGS:
                $result = $this->getAllowedForLogs();
                break;
        }
        return $result;
    }

    protected function getAllowedForFull()
    {
        $allowedLogs = $this->getAllowedForLogs();
        $allowedLoginAs = $this->getAllowedForLoginAs();
        $result = array_merge($allowedLogs, $allowedLoginAs);
        $result = array_unique($result);
        return $result;
    }

    protected function getAllowedForLoginAs()
    {
        return [
            'Magento_Backend::admin',
            'Magento_Customer::manage',
            'Magento_Customer::customer',
            'Magento_Sales::actions_view',
            'Magento_Sales::actions',
            'Magento_Sales::sales_order',
            'Magento_Sales::sales_operation',
            'Magento_Sales::sales',
            Cfg::ACL_RULE_LOGIN_AS
        ];
    }

    protected function getAllowedForLogs()
    {
        return [
            'Magento_Backend::admin',
            'Magento_Customer::manage',
            'Magento_Customer::customer',
            Cfg::ACL_RULE_LOGS
        ];
    }
}