<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Event
 *
 * Flexible entity for relation
 * event.event
 *
 * @see FlexibleEntity
 */
class Event extends FlexibleEntity
{
    private $dockets = [];

    public function __toString()
    {
        return $this->getName();
    }

    public function setEventType(EventType $eventType)
    {
        $this->eventType = $eventType;
    }

    public function setDockets($dockets)
    {
        $this->dockets = $dockets;
    }

    /**
     * __call
     *
     * Allows dynamic methods getXxx, setXxx, hasXxx, addXxx or clearXxx.
     *
     * @access  public
     * @throws  ModelException if method does not exist.
     * @param   mixed $method
     * @param   mixed $arguments
     * @return  mixed
     */
    public function __call($method, $arguments)
    {
        list($operation, $attribute) = $this->extractMethodName($method);
        foreach ($this->dockets as $docket) {
            if (strtolower($docket['name']) === strtolower($attribute)) {
                switch ($operation) {
                case 'get':
                    return $docket;
                default:
                    throw new ModelException(sprintf('No such method "%s:%s()"', get_class($this), $method));
                }
            }
        }
        return parent::__call($method, $arguments);
    }
}
