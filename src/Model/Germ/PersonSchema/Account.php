<?php

namespace GermBundle\Model\Germ\PersonSchema;

use Vibby\PommProjectFosUserBundle\Model\User;

/**
 * Account
 *
 * Flexible entity for relation
 * person.account
 *
 * @see FlexibleEntity
 */
class Account extends User
{
	public $keyForId = 'id_person_account';

    public function getId()
    {
        return $this->get($this->keyForId);
    }

    public function __toString()
    {
        return $this->getUsername();
    }
}
