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
	public function getRoles()
	{
		return [];
	}
}
