<?php

namespace Germ\Model\Germ\EventSchema;

use Germ\Model\Germ\EventSchema\Event;
use Germ\Model\Germ\EventSchema\EventModel;
use Germ\Model\Germ\EventSchema\Assignation;
use Germ\Model\Germ\EventSchema\AssignationModel;

use PommProject\ModelManager\ModelLayer\ModelLayer;

/**
 * EventModel
 *
 * Model class for table event.
 *
 * @see Model
 */
class EventModelLayer extends ModelLayer
{
    public function saveEvent(Event $event, array $properties)
    {
        $this->startTransaction();
        try {
            $this->getModel(EventModel::class)->updateOne($event, $properties);
            $assignationModel = $this->getModel(AssignationModel::class);
            foreach ($event->getAssignations() as $docketId => $personIds) {
                $assignationModel->removeAllForEvent($event, $docketId);
                foreach ($personIds as $personId) {
                    $assignation = new Assignation();
                    $assignation['person_id'] = $personId;
                    $assignation['event_id'] = $event->getId();
                    $assignation['docket_id'] = $docketId;
                    $assignationModel->insertOne($assignation);
                }
            }
            $this->commitTransaction();
        } catch (\Exception $e) {
            // If an exception is thrown, rollback everything and propage it.
            $this->rollbackTransaction();

            throw $e;
        }       
    }
}
