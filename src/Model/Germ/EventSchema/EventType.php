<?php

namespace Germ\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * EventType
 *
 * Flexible entity for relation
 * event.event_type
 *
 * @see FlexibleEntity
 */
class EventType extends FlexibleEntity
{
	public function __toString()
	{
		return $this->getName();
	}

    public function getId() {
        return $this->get('id_event_event_type');
    }
}
