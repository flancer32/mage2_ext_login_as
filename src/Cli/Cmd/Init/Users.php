<?php

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Flancer32\LoginAs\Cli\Cmd\Init\Users\Create as SubCreate;
use \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check as SubCheck;
use \Flancer32\LoginAs\Config as Cfg;

/**
 * Create test admin users for development deployment.
 */
class Users
    extends \Symfony\Component\Console\Command\Command
{
    const A_EMAIL = 'email';
    const A_NAME_FIRST = 'first';
    const A_NAME_LAST = 'last';
    const A_NAME_USER = 'name';
    const A_PASSWORD = 'password';
    const A_ROLE = 'role';

    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\Create */
    protected $subCreate;
    /** @var \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check */
    protected $subCheck;

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
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\Create $subCreate,
        \Flancer32\LoginAs\Cli\Cmd\Init\Users\Check $subCheck
    ) {
        /* object manager is used in __construct/configure */
        $this->manObj = $manObj;
        $this->subCreate = $subCreate;
        $this->subCheck = $subCheck;
        parent::__construct();
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('fl32:init:users');
        $this->setDescription("Create test users for 'Flancer32_LoginAs' module.");
        /* Magento related config (Object Manager) */
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->manObj->get(\Magento\Framework\App\State::class);
        try {
            /* area code should be set only once */
            $appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_ADMIN;
            $appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $this->manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $this->manObj->configure($config);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* check ACL Roles and get IDs */
        /* create context for process */
        $ctx = new \Flancer32\Lib\Data();
        $this->subCheck->exec($ctx);
        $mapRoles = $ctx->get(SubCheck::RES_ROLES);

        /* create users */
        foreach ($this->users as $user) {
            $username = $user[self::A_NAME_USER];
            $roleId = $mapRoles[$user[self::A_ROLE]];
            $output->writeln("Create user '$username'...");
            /* create context for process */
            $ctx = new \Flancer32\Lib\Data();
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
                $output->writeln("User '$username' is created.");
            } else {
                $output->writeln("User '$username' already exists.");
            }
        }

        $output->writeln('<info>Command is completed.<info>');
    }

}