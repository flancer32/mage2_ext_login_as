<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users;

use \Flancer32\LoginAs\Config as Cfg;
use \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check\Roles as SubRoles;

/**
 * Check existing ACL Roles.
 */
class Check
{
    const RES_ROLES = 'roles';

    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check\Roles */
    protected $subRoles;


    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check\Roles $subRoles
    ) {
        $this->subRoles = $subRoles;
    }

    public function exec(\Flancer32\Lib\Data $ctx)
    {
        /* create execution context and process Roles checking */
        $ctxRoles = new \Flancer32\Lib\Data();
        $this->subRoles->exec($ctxRoles);
        $result = $ctxRoles->get(SubRoles::RES_ROLES_MAP);

        /* save result to own execution context */
        $ctx->set(self::RES_ROLES, $result);
    }
}