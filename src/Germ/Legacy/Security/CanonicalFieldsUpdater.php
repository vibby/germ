<?php

namespace Germ\Legacy\Security;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\CanonicalFieldsUpdater as BaseCanonicalFieldsUpdater;

class CanonicalFieldsUpdater extends BaseCanonicalFieldsUpdater
{
    public function updateCanonicalFields(UserInterface $user)
    {
    }
}
