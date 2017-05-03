<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users\Check\Roles;

use \Flancer32\LoginAs\Config as Cfg;

/**
 * Refresh ACL rules for LoginAs Roles.
 */
class Acl
{
    /**
     * Input roles data ([roleCode=>roleId, ...]).
     */
    const CTX_ROLES_MAP = 'roles';
    const PERM_ALLOW = 'allow';
    const PERM_DENY = 'deny';
    /** @var \Magento\Framework\Acl\Builder */
    protected $aclBuilder;
    /** @var \Magento\Framework\AuthorizationInterface */
    protected $authorization;
    /** @var \Flancer32\Lib\Repo\Repo\IGeneric */
    protected $repoGeneric;
    /** @var \Magento\Framework\Authorization\RoleLocatorInterface */
    protected $roleLocator;

    public function __construct(
        \Magento\Framework\Acl\Builder $aclBuilder,
        \Flancer32\Lib\Repo\Repo\IGeneric $repoGeneric
    ) {
        $this->aclBuilder = $aclBuilder;
        $this->repoGeneric = $repoGeneric;
    }

    public function exec(\Flancer32\Lib\Data $ctx)
    {
        /* get working variables from context */
        $roles = $ctx->get(self::CTX_ROLES_MAP);

        /* load Magento ACL and get all available rules/resource */
        $acl = $this->aclBuilder->getAcl();
        $all = $acl->getResources();

        /* walk though LoginAs Roles and compose ACL Rules maps (allow/deny)*/
        foreach ($roles as $roleCode => $roleId) {
            $allowed = $this->getAllowed($roleCode);
            $toAllow = [];
            $toDeny = [];
            foreach ($all as $aclResource) {
                if (in_array($aclResource, $allowed)) {
                    $toAllow[] = $aclResource;
                } else {
                    $toDeny[] = $aclResource;
                }
            }
            /* clean up current ACL Rules for the Role and create new ones */
            $this->repoCleanForRole($roleId);
            $this->repoAddRules($roleId, $toAllow, self::PERM_ALLOW);
            $this->repoAddRules($roleId, $toDeny, self::PERM_DENY);
        }
    }

    /**
     * Get allowed ACL resources for LoginAs Role by role code.
     *
     * @param $roleCode
     * @return string[]
     */
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

    /**
     * Get allowed ACL resources for 'Full Access' Role.
     *
     * @return string[]
     */
    protected function getAllowedForFull()
    {
        $allowedLogs = $this->getAllowedForLogs();
        $allowedLoginAs = $this->getAllowedForLoginAs();
        $result = array_merge($allowedLogs, $allowedLoginAs);
        $result = array_unique($result);
        return $result;
    }

    /**
     * Get allowed ACL resources for 'Login Only' Role.
     *
     * @return string[]
     */
    protected function getAllowedForLoginAs()
    {
        return [
            'Magento_Backend::admin',
            'Magento_Backend::stores',
            'Magento_Backend::stores_settings',
            'Magento_Config::config',
            'Magento_Customer::config_customer',
            'Magento_Customer::customer',
            'Magento_Customer::manage',
            'Magento_Sales::actions_view',
            'Magento_Sales::actions',
            'Magento_Sales::sales',
            'Magento_Sales::sales_operation',
            'Magento_Sales::sales_order',
            Cfg::ACL_RULE_LOGIN_AS
        ];
    }

    /**
     * Get allowed ACL resources for 'Logs only' Role.
     *
     * @return string[]
     */
    protected function getAllowedForLogs()
    {
        return [
            'Magento_Backend::admin',
            'Magento_Customer::manage',
            'Magento_Customer::customer',
            Cfg::ACL_RULE_LOGS
        ];
    }

    /**
     * Add ACL Rules (resources for role and permission).
     *
     * @param int $roleId
     * @param string[] $resources
     * @param string $permission
     */
    protected function repoAddRules($roleId, $resources, $permission)
    {
        $entity = Cfg::ENTITY_AUTH_RULE;
        $bind = [
            Cfg::E_AUTH_RULE_A_ROLE_ID => (int)$roleId,
            Cfg::E_AUTH_RULE_A_PERMISSION => $permission
        ];
        foreach ($resources as $resource) {
            $bind[Cfg::E_AUTH_RULE_A_RESOURCE_ID] = $resource;
            $this->repoGeneric->addEntity($entity, $bind);
        }
    }

    /**
     * Clean up all rules for the role.
     *
     * @param int $roleId
     */
    protected function repoCleanForRole($roleId)
    {
        $entity = Cfg::ENTITY_AUTH_RULE;
        $where = Cfg::E_AUTH_RULE_A_ROLE_ID . '=' . (int)$roleId;
        $this->repoGeneric->deleteEntity($entity, $where);
    }
}