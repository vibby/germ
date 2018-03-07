<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\FlexibleEntity;
use PommProject\ModelManager\Exception\ModelException;

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
    private $assignations = [];

    public function __toString()
    {
        return $this->getName();
    }

    public function setDockets($dockets)
    {
        $this->dockets = $dockets;
    }

    public function getDockets()
    {
        return $this->dockets;
    }

    public function getAssignations()
    {
        return $this->assignations;
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
            if (strtolower('docket_'.$docket['name']) === strtolower($attribute)) {
                switch ($operation) {
                case 'get':
                    return isset ($docket['id_person_person']) ? $docket['id_person_person'] : null;
                case 'set':
                    $accountId = $arguments[0];
                    if ($accountId) {
                        $this->assignations[$docket['id']] = [$accountId];
                    }
                    return true;
                default:
                    throw new ModelException(sprintf('No such method "%s:%s()"', get_class($this), $method));
                }
            }
        }
        return parent::__call($method, $arguments);
    }

    public function getId() {
        return $this->get('id_event_event');
    }
}
