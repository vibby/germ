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
    	try {
	        return (string) $this->getAccountId();
    	} catch (\Exception $e) {
    		return 'test';
    	}
    }
}
