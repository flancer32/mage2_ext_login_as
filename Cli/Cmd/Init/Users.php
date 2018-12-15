<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

use Flancer32\LoginAs\Cli\Cmd\Init\Users\Check as SubCheck;
use Flancer32\LoginAs\Cli\Cmd\Init\Users\Create as SubCreate;
use Flancer32\LoginAs\Config as Cfg;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create test admin users for development deployment.
 */
class Users
    extends \Flancer32\LoginAs\Cli\Cmd\Base
{
    const A_EMAIL = 'email';
    const A_NAME_FIRST = 'first';
    const A_NAME_LAST = 'last';
    const A_NAME_USER = 'name';
    const A_PASSWORD = 'password';
    const A_ROLE = 'role';
    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check */
    protected $subCheck;
    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\Create */
    protected $subCreate;
    /**
     * Hardcoded data for initial test users.
     */
    protected $users = [
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
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\Create $subCreate,
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check $subCheck
    ) {
        parent::__construct(self::class);
        $this->subCreate = $subCreate;
        $this->subCheck = $subCheck;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('fl32:init:users');
        $this->setDescription("Create test users for 'Flancer32_LoginAs' module.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Create tests users and roles for \'Flancer32_LoginAs\' module.<info>');
        /* check ACL Roles and get IDs (create context for process) */
        $ctx = new \Magento\Framework\DataObject();
        $this->subCheck->exec($ctx);
        /* get result from context */
        $mapRoles = $ctx->get(SubCheck::RES_ROLES);

        /* Create users or update user's roles */
        foreach ($this->users as $user) {
            $username = $user[self::A_NAME_USER];
            $password = $user[self::A_PASSWORD];
            $roleId = $mapRoles[$user[self::A_ROLE]];
            $output->writeln("Create user '$username'...");
            /* create context for process */
            $ctx = new \Magento\Framework\DataObject();
            $ctx->set(SubCreate::OPT_USER_NAME, $username);
            $ctx->set(SubCreate::OPT_PASSWORD, $user[self::A_PASSWORD]);
            $ctx->set(SubCreate::OPT_EMAIL, $user[self::A_EMAIL]);
            $ctx->set(SubCreate::OPT_NAME_FIRST, $user[self::A_NAME_FIRST]);
            $ctx->set(SubCreate::OPT_NAME_LAST, $user[self::A_NAME_LAST]);
            $ctx->set(SubCreate::OPT_ROLE_ID, $roleId);
            /* perform action */
            $this->subCreate->exec($ctx);
            $isCreated = $ctx->get(SubCreate::RES_CREATED_AS_NEW);

            /* analyze results */
            if ($isCreated) {
                $output->writeln("User '$username' with password '$password' is created.");
            } else {
                $output->writeln("User '$username' already exists.");
            }
        }

        $output->writeln('<info>Command is completed.<info>');
    }

}