<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Member
 *
 * Flexible entity for relation
 * public.member
 *
 * @see FlexibleEntity
 */
class Member extends FlexibleEntity
{
	public function getName()
	{
		return $this->getFirstname() . ' ' . $this->getLastname();
	}
}
