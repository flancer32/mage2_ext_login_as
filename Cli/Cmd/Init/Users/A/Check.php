<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users\A;

/**
 * Check existing ACL Roles.
 */
class Check
{
    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check\A\Roles */
    private $aRoles;


    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check\A\Roles $aRoles
    ) {
        $this->aRoles = $aRoles;
    }

    public function exec()
    {
        /* create execution context and process Roles checking */
        $result = $this->aRoles->exec();
        return $result;
    }
}