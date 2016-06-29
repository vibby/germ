<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Person
 *
 * Flexible entity for relation
 * public.person
 *
 * @see FlexibleEntity
 */
class Person extends FlexibleEntity
{
	public function __toString()
	{
		return $this->getFirstname() . ' ' . $this->getLastname();
	}
}
