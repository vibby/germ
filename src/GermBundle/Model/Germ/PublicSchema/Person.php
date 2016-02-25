<?php

namespace GermBundle\Model\Germ\PublicSchema;

use Vibby\PommProjectFosUserBundle\Model\User;

/**
 * Person
 *
 * Flexible entity for relation
 * public.person
 *
 * @see FlexibleEntity
 */
class Person extends User
{
	public function getName()
	{
		return $this->getFirstname() . ' ' . $this->getLastname();
	}
}
