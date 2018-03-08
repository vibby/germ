<?php

namespace Germ\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Germ\Model\Germ\EventSchema\Event;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EventController extends Controller
{

    public function listAction(Request $request)
    {
        $model = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
        $events = $model->findAll('order by date_from desc');

        $model = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\EventSchema\EventTypeModel');
        $eventTypes = $model->findAll();

        return $this->render(
            'Germ:Event:list.html.twig',
            array(
                'events'     => $events,
                'eventTypes' => $eventTypes,
            )
        );
    }

    public function editAction(Request $request, $eventId)
    {
        $translator = $this->get('translator');
        $event = $this->getEventOr404($eventId);

        $eventForm = $this->buildEventForm($event);
        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $eventModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
            $propeties = $eventForm->getData()->extract();
            unset($propeties['event_type_name']);
            unset($propeties['event_type_layout']);
            unset($propeties['location_name']);
            //dump($propeties);die;
            $eventModel = $this->get('pomm')['germ']->getModelLayer('Germ\Model\Germ\EventSchema\EventModelLayer');
            $eventModel->saveEvent($event, array_keys($propeties));
            $this->get('session')->getFlashBag()->add('success', 'Event updated');
            return $this->redirectToRoute('germ_event_edit', ['eventId' => $event->getId()]);
        }
        $eventForm = $eventForm->createView();

        return $this->render(
            'Germ:Event:edit.html.twig',
            array(
                'mode' => 'edit',
                'form' => $eventForm,
            )
        );
    }

    public function showAction(Request $request, $eventId)
    {
        $eventModel = $this->get('pomm')['germ']
            ->getModel('Germ\Model\Germ\EventSchema\EventModel');
        $event = $this->getEventOr404($eventId);
        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }

        return $this->render(
            'Germ:Event:show.html.twig',
            array(
                'event' => $event,
            )
        );
    }

    public function createAction(Request $request)
    {
        $event = $this->buildNextEvent($request->get('event_type'));
        $form = $this->buildEventForm($event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
            $propeties = array_keys($form->getData()->extract());
            unset($propeties['event_type_name']);
            $eventModel->insertOne($event, array_keys($form->getData()->extract()));
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Event created'));
            return $this->redirectToRoute('germ_event_edit', ['eventId' => $event->getId()]);
        }

        return $this->render(
            'Germ:Event:edit.html.twig',
            array(
                'form' => $form->createView(),
                'mode' => 'create',
            )
        );
    }

    private function getEventOr404($eventId)
    {
        $eventModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
        $event = $eventModel->getEventById($eventId);
        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }
        return $event;
    }

    private function buildEventCreateForm($eventTypeId)
    {
    }

    private function buildNextEvent($eventTypeId)
    {
        $eventTypeModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\EventSchema\EventTypeModel');
        $type = $eventTypeModel->findByPk(['id_event_event_type' => $eventTypeId]);

        $event = new Event();
        $event['type_id'] = $eventTypeId;
        $event['event_type_name'] = $type['name'];
        $event['name'] = '';
        $event['date_from'] = new \DateTime;
        $event['duration'] = new \DateInterval('PT1H');

        return $event;
    }

    private function buildEventForm(Event $event)
    {
        $eventModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
        $event = $eventModel->hydrateDockets($event);

        $personModel = $this->get('pomm')['germ']->getModel('Germ\Model\Germ\PersonSchema\PersonModel');
        $personChoices['-'.$this->get('translator')->trans('none').'-'] = null;
        foreach ($personModel->getPersons() as $key => $person) {
            $personChoices[(string) $person] = $person->getId();
        }

        $builder = $this->get('form.factory')
            ->createNamedBuilder('Event', FormType::class, $event)
            ->add(
                'event_type_name',
                TextType::class,
                [
                    'attr' => ['readonly' => true],
                ]
            )
            ->add('name', TextType::class)
            ->add('date_from', DateType::class)
            ->add(
                'duration',
                DateIntervalType::class,
                [
                    'with_years' => false,
                    'with_months' => false,
                    'with_weeks' => false,
                    'with_days' => false,
                    'with_hours' => true,
                    'with_minutes' => true,
                ]
            )
        ;
        foreach ($event->getDockets() as $docket) {
            $builder->add(
                'docket_'.$docket->getName(),
                ChoiceType::class,
                [
                    'choices'      => $personChoices,
                    'choice_translation_domain' => false,
                ]
            );
        }

        return $builder->getForm();
    }
}
