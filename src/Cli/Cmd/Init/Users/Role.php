<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users;

use \Flancer32\LoginAs\Config as Cfg;

/**
 * Simple process that creates new ACL Roles.
 */
class Role
{
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
        $roleColl = $this->modRole->getCollection();
        $items = $roleColl->getItems();
        /* compose array with Roles to create */
        $rolesToCreate = array_flip($this->roles);
        foreach ($items as $item) {
            $type = $item->getRoleType();
            if ($type == 'G') {
                $name = $item->getRoleName();
                if (isset($rolesToCreate[$name])) {
                    unset($rolesToCreate[$name]);
                }
            }
        }
        /* create unexisting Roles */
        foreach ($rolesToCreate as $name => $code) {
            $this->modRole->unsetData();
            $this->modRole->setRoleName($name);
            $this->modRole->setRoleType('G');
            $this->modRole->getResource()->save($this->modRole);
            $id = $this->modRole->getId();
        }
    }
}