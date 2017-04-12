<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users\Check;

use \Flancer32\LoginAs\Config as Cfg;

/**
 * Get existing ACL Roles and create new ones if required.
 */
class Roles
{
    const RES_ROLES_MAP = 'rolesMap';
    const ROLE_TYPE_GROUP = 'G';

    /** @var \Magento\Authorization\Model\Role */
    protected $modRole;

    protected $roles = [
        Cfg::ACL_ROLE_FULL => 'Flancer32 LoginAs Full Access',
        Cfg::ACL_ROLE_LOGIN => 'Flancer32 LoginAs Login Only',
        Cfg::ACL_ROLE_LOGS => 'Flancer32 LoginAs Logs Only'
    ];

    public function __construct(
        \Magento\Authorization\Model\Role $modRole
    ) {
        $this->modRole = $modRole;
    }

    public function exec(\Flancer32\Lib\Data $ctx)
    {
        $result = [];
        $roleColl = $this->modRole->getCollection();
        $items = $roleColl->getItems();
        /* compose array with Roles to create */
        $rolesToCreate = array_flip($this->roles);
        /* walk through exisitng roles and collect data */
        /** @var \Magento\Authorization\Model\Role\Interceptor $item */
        foreach ($items as $item) {
            $type = $item->getRoleType();
            if ($type == self::ROLE_TYPE_GROUP) {
                $roleName = $item->getRoleName();
                if (isset($rolesToCreate[$roleName])) {
                    $roleCode = $rolesToCreate[$roleName];
                    $roleId = $item->getId();
                    $result[$roleCode] = $roleId;
                    /* remove existing roles from 'to be created' list*/
                    unset($rolesToCreate[$roleName]);
                }
            }
        }
        /* create un-existing Roles */
        $created = $this->createMissedRoles($rolesToCreate);
        $result = array_merge($result, $created);

        /* walk through roles and check ACL resources assigned */
        /* save result to context */
        $ctx->set(self::RES_ROLES_MAP, $result);
    }

    /**
     * @param array $rolesToCreate [$roleName => $roleCode, ...]
     */
    protected function createMissedRoles($rolesToCreate)
    {
        $result = [];
        foreach ($rolesToCreate as $roleName => $roleCode) {
            $this->modRole->unsetData();
            $this->modRole->setRoleName($roleName);
            $this->modRole->setRoleType(self::ROLE_TYPE_GROUP);
            $this->modRole->getResource()->save($this->modRole);
            $roleId = $this->modRole->getId();
            $result[$roleCode] = $roleId;
        }
        return $result;
    }
}