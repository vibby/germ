<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * EventType
 *
 * Flexible entity for relation
 * public.event_type
 *
 * @see FlexibleEntity
 */
class EventType extends FlexibleEntity
{
	public function __toString()
	{
		return $this->getName();
	}
}
