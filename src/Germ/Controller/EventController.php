<?php

namespace Germ\Controller;

use Germ\Model\Germ\EventSchema\Event;
use Germ\Model\Germ\EventSchema\EventModel;
use Germ\Model\Germ\EventSchema\EventTypeModel;
use PommProject\Foundation\Pomm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventController extends AbstractController
{
    private $pomm;
    private $translator;
    private $formFactory;

    public function __construct(Pomm $pomm, TranslatorInterface $translator, FormFactoryInterface $formFactory)
    {
        $this->pomm = $pomm;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
    }

    public function listAction(Request $request)
    {
        $model = $this->pomm['germ']->getModel(EventModel::class);
        $events = $model->findAll('order by date_from desc');

        $model = $this->pomm['germ']->getModel(EventTypeModel::class);
        $eventTypes = $model->findAll();

        return $this->render(
            'Event/list.html.twig',
            [
                'events' => $events,
                'eventTypes' => $eventTypes,
            ]
        );
    }

    public function editAction(Request $request, $eventId)
    {
        $event = $this->getEventOr404($eventId);

        $eventForm = $this->buildEventForm($event);
        $eventForm->handleRequest($request);
        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            $properties = $eventForm->getData()->extract();
            unset($properties['event_type_name']);
            unset($properties['event_type_layout']);
            unset($properties['location_name']);
            $eventModel = $this->pomm['germ']->getModelLayer('Germ\Model\Germ\EventSchema\EventModelLayer');
            $eventModel->saveEvent($event, array_keys($properties));
            $request->getSession()->getFlashBag()->add('success', 'Event updated');

            return $this->redirectToRoute('germ_event_edit', ['eventId' => $event->getId()]);
        }
        $eventForm = $eventForm->createView();

        return $this->render(
            'Event/edit.html.twig',
            [
                'mode' => 'edit',
                'form' => $eventForm,
            ]
        );
    }

    public function showAction(Request $request, $eventId)
    {
        $eventModel = $this->pomm['germ']
            ->getModel('Germ\Model\Germ\EventSchema\EventModel');
        $event = $this->getEventOr404($eventId);
        if (! $event) {
            throw $this->createNotFoundException('The event does not exist');
        }

        return $this->render(
            'Event/show.html.twig',
            [
                'event' => $event,
            ]
        );
    }

    public function createAction(Request $request)
    {
        $event = $this->buildNextEvent($request->get('event_type'));
        $form = $this->buildEventForm($event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventModel = $this->pomm['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
            $propeties = array_keys($form->getData()->extract());
            unset($propeties['event_type_name']);
            $eventModel->insertOne($event, array_keys($form->getData()->extract()));
            $request->getSession()->getFlashBag()->add('success', $this->translator->trans('Event created'));

            return $this->redirectToRoute('germ_event_edit', ['eventId' => $event->getId()]);
        }

        return $this->render(
            'Event/edit.html.twig',
            [
                'form' => $form->createView(),
                'mode' => 'create',
            ]
        );
    }

    private function getEventOr404($eventId)
    {
        $eventModel = $this->pomm['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
        $event = $eventModel->getEventById($eventId);
        if (! $event) {
            throw $this->createNotFoundException('The event does not exist');
        }

        return $event;
    }

    private function buildEventCreateForm($eventTypeId)
    {
    }

    private function buildNextEvent($eventTypeId)
    {
        $eventTypeModel = $this->pomm['germ']->getModel('Germ\Model\Germ\EventSchema\EventTypeModel');
        $type = $eventTypeModel->findByPk(['id_event_event_type' => $eventTypeId]);

        $event = new Event();
        $event['type_id'] = $eventTypeId;
        $event['event_type_name'] = $type['name'];
        $event['name'] = '';
        $event['date_from'] = new \DateTime();
        $event['duration'] = new \DateInterval('PT1H');

        return $event;
    }

    private function buildEventForm(Event $event)
    {
        $eventModel = $this->pomm['germ']->getModel('Germ\Model\Germ\EventSchema\EventModel');
        $event = $eventModel->hydrateDockets($event);

        $personModel = $this->pomm['germ']->getModel('Germ\Model\Germ\PersonSchema\PersonModel');
        $personChoices['-'.$this->translator->trans('none').'-'] = null;
        foreach ($personModel->getPersons() as $key => $person) {
            $personChoices[(string) $person] = $person->getId();
        }

        $builder = $this->formFactory
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
            );
        foreach ($event->getDockets() as $docket) {
            $builder->add(
                'docket_'.$docket->getName(),
                ChoiceType::class,
                [
                    'choices' => $personChoices,
                    'choice_translation_domain' => false,
                ]
            );
        }

        return $builder->getForm();
    }
}
