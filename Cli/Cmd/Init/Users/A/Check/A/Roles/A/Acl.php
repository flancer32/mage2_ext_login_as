<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check\A\Roles\A;

use Flancer32\LoginAs\Config as Cfg;

/**
 * Refresh ACL rules for LoginAs Roles.
 */
class Acl
{
    /**
     * Input roles data ([roleCode=>roleId, ...]).
     */
    private const PERM_ALLOW = 'allow';
    private const PERM_DENY = 'deny';

    /** @var \Magento\Framework\Acl\Builder */
    private $aclBuilder;
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\Acl\Builder $aclBuilder,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->aclBuilder = $aclBuilder;
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
    }

    private function addAuthRule($roleId, $resourceId, $permission)
    {
        $entity = Cfg::ENTITY_AUTH_RULE;
        $table = $this->resource->getTableName($entity);
        $bind = [
            Cfg::E_AUTH_RULE_A_ROLE_ID => (int)$roleId,
            Cfg::E_AUTH_RULE_A_PERMISSION => $permission,
            Cfg::E_AUTH_RULE_A_RESOURCE_ID => $resourceId
        ];
        $this->conn->insert($table, $bind);
    }

    public function exec($roles)
    {
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
    private function getAllowed($roleCode)
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

    private function getAllowedCommon()
    {
        return [
            'Magento_AdminNotification::adminnotification',
            'Magento_AdminNotification::adminnotification_remove',
            'Magento_AdminNotification::mark_as_read',
            'Magento_AdminNotification::show_list',
            'Magento_AdminNotification::show_toolbar',
            'Magento_Backend::admin',
            'Magento_Backend::marketing',
            'Magento_Backend::marketing_communications',
            'Magento_Backend::marketing_user_content',
            'Magento_Backend::stores',
            'Magento_Backend::stores_settings',
            'Magento_Config::config',
            'Magento_Customer::customer',
            'Magento_Customer::manage',
            'Magento_Newsletter::newsletter',
            'Magento_Newsletter::subscriber',
            'Magento_Review::reviews_all'
        ];
    }

    /**
     * Get allowed ACL resources for 'Full Access' Role.
     *
     * @return string[]
     */
    private function getAllowedForFull()
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
    private function getAllowedForLoginAs()
    {
        $allowedCommon = $this->getAllowedCommon();
        $allowedThis = [
            'Magento_Sales::actions',
            'Magento_Sales::actions_view',
            'Magento_Sales::sales',
            'Magento_Sales::sales_operation',
            'Magento_Sales::sales_order',
            Cfg::ACL_RULE_CONFIG,
            Cfg::ACL_RULE_LOGIN_AS
        ];
        $result = array_merge($allowedCommon, $allowedThis);
        $result = array_unique($result);
        return $result;
    }

    /**
     * Get allowed ACL resources for 'Logs only' Role.
     *
     * @return string[]
     */
    private function getAllowedForLogs()
    {
        $allowedCommon = $this->getAllowedCommon();
        $allowedThis = [
            Cfg::ACL_RULE_LOGS
        ];
        $result = array_merge($allowedCommon, $allowedThis);
        $result = array_unique($result);
        return $result;
    }

    /**
     * Add ACL Rules (resources for role and permission).
     *
     * @param int $roleId
     * @param string[] $resources
     * @param string $permission
     */
    private function repoAddRules($roleId, $resources, $permission)
    {
        foreach ($resources as $resource) {
            $this->addAuthRule($roleId, $resource, $permission);
        }
    }

    /**
     * Clean up all rules for the role.
     *
     * @param int $roleId
     */
    private function repoCleanForRole($roleId)
    {
        $entity = Cfg::ENTITY_AUTH_RULE;
        $table = $this->resource->getTableName($entity);
        $where = Cfg::E_AUTH_RULE_A_ROLE_ID . '=' . (int)$roleId;
        $this->conn->delete($table, $where);
    }
}