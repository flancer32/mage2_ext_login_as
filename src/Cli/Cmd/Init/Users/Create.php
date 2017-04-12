<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users;

/**
 * Simple process that creates new Admin User with given role.
 */
class Create
{
    const OPT_EMAIL = 'email';
    const OPT_NAME_FIRST = 'name_first';
    const OPT_NAME_LAST = 'name_last';
    const OPT_PASSWORD = 'password';
    const OPT_ROLE_ID = 'role_id';
    const OPT_USER_NAME = 'user_name';
    /* boolean: true - new user is created, 'false' - user with the same name already exists */
    const RES_CREATED_AS_NEW = 'created_as_new';

    /** @var \Magento\User\Model\UserFactory */
    protected $factoryUser;

    public function __construct(
        \Magento\User\Model\UserFactory $factoryUser
    ) {
        $this->factoryUser = $factoryUser;
    }

    public function exec(\Flancer32\Lib\Data $ctx)
    {
        /* get input options from context */
        $username = $ctx->get(self::OPT_USER_NAME);
        $nameFirst = $ctx->get(self::OPT_NAME_FIRST);
        $nameLast = $ctx->get(self::OPT_NAME_LAST);
        $password = $ctx->get(self::OPT_PASSWORD);
        $email = $ctx->get(self::OPT_EMAIL);
        $roleId = $ctx->get(self::OPT_ROLE_ID);

        /* perform requested action */
        $user = $this->factoryUser->create();
        $user->loadByUsername($username);
        $userCreated = null;
        if ($username != $user->getUserName()) {
            $user->setFirstName($nameFirst);
            $user->setLastName($nameLast);
            $user->setUserName($username);
            $user->setPassword($password);
            $user->setEmail($email);
            $user->setRoleId($roleId);
            $user->save();
            $userCreated = true;
        } else {
            $userCreated = false;
            /* check role ID */
            $role = $user->getRole();
            if ($role->getId() != $roleId) {
                $user->setRoleId($roleId);
                $user->save();
            }
        }

        /* place results back to context */
        $ctx->set(self::RES_CREATED_AS_NEW, $userCreated);
    }
}