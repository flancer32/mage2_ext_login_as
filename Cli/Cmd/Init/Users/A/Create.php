<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Users\A;

/**
 * Simple process that creates new Admin User with given role.
 */
class Create
{
    /** @var \Magento\User\Model\UserFactory */
    private $factUser;

    public function __construct(
        \Magento\User\Model\UserFactory $factUser
    ) {
        $this->factUser = $factUser;
    }

    public function exec(
        $username, $nameFirst, $nameLast,
        $password, $email, $roleId
    )
    {
        /* perform requested action */
        $user = $this->factUser->create();
        $user->loadByUsername($username);
        $isUserCreated = null;
        if ($username != $user->getUserName()) {
            $user->setFirstName($nameFirst);
            $user->setLastName($nameLast);
            $user->setUserName($username);
            $user->setPassword($password);
            $user->setEmail($email);
            $user->setRoleId($roleId);
            $user->save();
            $isUserCreated = true;
        } else {
            $isUserCreated = false;
            /* check role ID */
            $role = $user->getRole();
            if ($role->getId() != $roleId) {
                $user->setRoleId($roleId);
                $user->save();
            }
        }

        return $isUserCreated;
    }
}