<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Event
 *
 * Flexible entity for relation
 * public.event
 *
 * @see FlexibleEntity
 */
class Event extends FlexibleEntity
{
	public function __toString()
	{
		return $this->getName();
	}
}
