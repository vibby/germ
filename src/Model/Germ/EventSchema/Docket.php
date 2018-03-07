<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Docket
 *
 * Flexible entity for relation
 * event.docket
 *
 * @see FlexibleEntity
 */
class Docket extends FlexibleEntity
{
    public function __toString()
    {
        return (string) $this->getAccountId();
    }

    public function getId() {
        return $this->get('id_event_docket');
    }
}
