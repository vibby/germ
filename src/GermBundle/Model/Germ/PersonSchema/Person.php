<?php

namespace GermBundle\Model\Germ\PersonSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Person
 *
 * Flexible entity for relation
 * person.person
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
