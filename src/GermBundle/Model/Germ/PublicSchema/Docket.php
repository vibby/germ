<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Docket
 *
 * Flexible entity for relation
 * public.docket
 *
 * @see FlexibleEntity
 */
class Docket extends FlexibleEntity
{
    public function __toString()
    {
        return $this->getAccountId();
    }
}
