<?php

namespace GermBundle\Security;

use Vibby\PommProjectFosUserBundle\Manager\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserInterface;

class UserManager extends BaseUserManager
{
    public function updateUser(UserInterface $user, $insertInDb = false)
    {
        if ($user->has($this->pommModel->keyForId)) {
            $result = $this->pommModel->updateOne($user, ['salt', 'password']);
        } else {
            $this->updatePassword($user);
            $this->pommModel->insertOne($user);
        }
    }
}
