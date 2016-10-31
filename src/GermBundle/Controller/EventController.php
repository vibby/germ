<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GermBundle\Model\Germ\EventSchema\Event;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use GermBundle\Type\DateIntervalType;
// TODO : replace previous line with the above when sf 3.2 is out
// use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EventController extends Controller
{

    public function listAction(Request $request)
    {
        $model = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\EventSchema\EventModel');
        $events = $model->findAll('order by date_from desc');

        $model = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\EventSchema\EventTypeModel');
        $eventTypes = $model->findAll();

        return $this->render(
            'GermBundle:Event:list.html.twig',
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
            $eventModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\EventSchema\EventModel');
            $eventModel->updateOne($event, array_keys($eventForm->getData()->extract()));
            $this->get('session')->getFlashBag()->add('success', 'Event updated');
            return $this->redirectToRoute('germ_event_edit', ['eventId' => $event->getId()]);
        }
        $eventForm = $eventForm->createView();

        return $this->render(
            'GermBundle:Event:edit.html.twig',
            array(
                'mode' => 'edit',
                'form' => $eventForm,
            )
        );
    }

    public function showAction(Request $request, $eventId)
    {
        $eventModel = $this->get('pomm')['germ']
            ->getModel('GermBundle\Model\Germ\EventSchema\EventModel');
        $event = $this->getEventOr404($eventId);
        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }

        return $this->render(
            'GermBundle:Event:show.html.twig',
            array(
                'event' => $event,
            )
        );
    }

    public function createAction(Request $request)
    {
        $event = new Event();
        $event->setName('');
        $event->setDateFrom(new \DateTime());
        $event->setDuration(new \DateInterval('PT1H'));
        $form = $this->buildEventForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\EventSchema\EventModel');
            $eventModel->insertOne($event, array_keys($form->getData()->extract()));
            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('Event created'));
            return $this->redirectToRoute('germ_event_edit', ['eventId' => $event->getId()]);
        }

        return $this->render(
            'GermBundle:Event:edit.html.twig',
            array(
                'form' => $form->createView(),
                'mode' => 'create',
            )
        );
    }

    private function getEventOr404($eventId)
    {
        $eventModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\EventSchema\EventModel');
        $event = $eventModel->getEventById($eventId);
        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }
        return $event;
    }

    private function buildEventForm($event)
    {
        $docketModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\EventSchema\DocketModel');
        $dockets = $docketModel->getDocketsAndAssignationsForEvent($event);
        $event->setDockets($dockets);

        $accountModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PersonSchema\AccountModel');
        foreach ($accountModel->getAccounts() as $key => $account) {
            $accountLabels[$key] = $account->getPersonName();
            $accountChoices[$key] = $account->getId();
        }

        $builder = $this->get('form.factory')
            ->createNamedBuilder('Event', FormType::class, $event)
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
            ->add(
                'event_type_name',
                TextType::class,
                [
                    'attr' => ['readonly' => true],
                ]
            );
        foreach ($dockets as $docket) {
            $builder->add(
                $docket->getName(),
                ChoiceType::class,
                [
                    'choices'      => $accountChoices,
                    'choice_label' => function ($value, $key, $index) use ($accountLabels) {
                        return $accountLabels[$key];
                    },
                    'choice_translation_domain' => false,
                ]
            );
        }

        return $builder->getForm();
    }
}
