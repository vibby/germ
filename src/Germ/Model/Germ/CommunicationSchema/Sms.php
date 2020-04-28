<?php

namespace Germ\Model\Germ\CommunicationSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Sms
 *
 * Flexible entity for relation
 * communication.sms
 *
 * @see FlexibleEntity
 */
class Sms extends FlexibleEntity
{
    const STATUS_UNSENT = 'unsent';

    public function __construct(array $values = [])
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        parent::__construct(array_merge(
            $values,
            [
                'date' => $date,
                'status' => self::STATUS_UNSENT,
            ]
        ));
    }
}
