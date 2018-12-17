<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

use Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check as SubCheck;
use Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Create as SubCreate;
use Flancer32\LoginAs\Config as Cfg;

/**
 * Create test admin users for development deployment.
 */
class Users
    extends \Flancer32\Base\App\Cli\Base
{
    private const DESC = 'Create test users for \'Flancer32_LoginAs\' module.';
    private const NAME = 'fl32:init:users';

    private const A_EMAIL = 'email';
    private const A_NAME_FIRST = 'first';
    private const A_NAME_LAST = 'last';
    private const A_NAME_USER = 'name';
    private const A_PASSWORD = 'password';
    private const A_ROLE = 'role';

    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check */
    private $aCheck;
    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Create */
    private $aCreate;
    /**
     * Hardcoded data for initial test users.
     */
    private $users = [
        [
            self::A_NAME_USER => 'fl32_loginas_full',
            self::A_PASSWORD => 'Ss4N1i1Poq8bOjzbcOWi',
            self::A_EMAIL => 'loginas.full@flancer32.com',
            self::A_NAME_FIRST => 'LoginAs',
            self::A_NAME_LAST => 'Full Access',
            self::A_ROLE => Cfg::ACL_ROLE_FULL
        ],
        [
            self::A_NAME_USER => 'fl32_loginas_login',
            self::A_PASSWORD => '0WIPgwSx0o69AOJhOj66',
            self::A_EMAIL => 'loginas.login@flancer32.com',
            self::A_NAME_FIRST => 'LoginAs',
            self::A_NAME_LAST => 'Login Only',
            self::A_ROLE => Cfg::ACL_ROLE_LOGIN
        ],
        [
            self::A_NAME_USER => 'fl32_loginas_logs',
            self::A_PASSWORD => 'kzRG8pPrgHpPdXTAlnxm',
            self::A_EMAIL => 'loginas.logs@flancer32.com',
            self::A_NAME_FIRST => 'LoginAs',
            self::A_NAME_LAST => 'Logs Only',
            self::A_ROLE => Cfg::ACL_ROLE_LOGS
        ]
    ];

    public function __construct(
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Create $aCreate,
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\A\Check $aCheck
    ) {
        parent::__construct(self::NAME, self::DESC);
        $this->aCreate = $aCreate;
        $this->aCheck = $aCheck;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $output->writeln("<info>Command '" . $this->getName() . "':<info>");
        /* check ACL Roles and get IDs (create context for process) */
        $mapRoles = $this->aCheck->exec();

        /* Create users or update user's roles */
        foreach ($this->users as $user) {
            $username = $user[self::A_NAME_USER];
            $password = $user[self::A_PASSWORD];
            $roleId = $mapRoles[$user[self::A_ROLE]];
            $output->writeln("Create user '$username'...");
            /* perform action */
            $isCreated =  $this->aCreate->exec(
                $username,
                $user[self::A_NAME_FIRST],
                $user[self::A_NAME_LAST],
                $user[self::A_PASSWORD],
                $user[self::A_EMAIL],
                $roleId
            );

            /* analyze results */
            if ($isCreated) {
                $output->writeln("User '$username' with password '$password' is created.");
            } else {
                $output->writeln("User '$username' already exists.");
            }
        }

        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

}