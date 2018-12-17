<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check\A;

use Flancer32\LoginAs\Config as Cfg;

/**
 * Get existing ACL Roles and create new ones if required.
 */
class Roles
{
    private const RES_ROLES_MAP = 'rolesMap';
    private const ROLE_TYPE_GROUP = 'G';

    /** @var \Magento\Authorization\Model\Role */
    private $modRole;

    private $roles = [
        Cfg::ACL_ROLE_FULL => 'Flancer32 LoginAs Full Access',
        Cfg::ACL_ROLE_LOGIN => 'Flancer32 LoginAs Login Only',
        Cfg::ACL_ROLE_LOGS => 'Flancer32 LoginAs Logs Only'
    ];
    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check\A\Roles\A\Acl */
    private $anAcl;

    public function __construct(
        \Magento\Authorization\Model\Role $modRole,
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check\A\Roles\A\Acl $anAcl
    ) {
        $this->modRole = $modRole;
        $this->anAcl = $anAcl;
    }

    /**
     * @param array $rolesToCreate [$roleName => $roleCode, ...]
     * @return array
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function createMissedRoles($rolesToCreate)
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

    public function exec()
    {
        $result = [];
        $roleColl = $this->modRole->getCollection();
        $items = $roleColl->getItems();
        /* compose array with Roles to create */
        $rolesToCreate = array_flip($this->roles);
        /* walk through existing roles and collect data */
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
        $this->anAcl->exec($result);

        return $result;
    }
}