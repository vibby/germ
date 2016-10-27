<?php

namespace GermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GermBundle\Model\Germ\PublicSchema\Event;
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
        $model = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PublicSchema\EventModel');
        $events = $model->findAll('order by date_from desc');

        $model = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PublicSchema\EventTypeModel');
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
            $eventModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PublicSchema\EventModel');
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
            ->getModel('GermBundle\Model\Germ\PublicSchema\EventModel');
        $event = $eventModel->findByPK(['id'=>$eventId]);
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
        $event->setDateFrom('');
        $event->setDuration('');
        $form = $this->buildEventForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PublicSchema\EventModel');
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
        $eventModel = $this->get('pomm')['germ']->getModel('GermBundle\Model\Germ\PublicSchema\EventModel');
        $event = $eventModel->findByPK(['id'=>$eventId]);
        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }
        return $event;
    }

    private function buildEventForm(Event $event)
    {
        $builder = $this->get('form.factory')
            ->createNamedBuilder('Event', FormType::class, $event)
            ->add('name', TextType::class)
            ->add('date_from', DateType::class)
            ->add('duration', DateIntervalType::class);

        // $builder->get('date_from')
        //     ->addModelTransformer(new CallbackTransformer(
        //         function (/DateTime $date) {
        //             return $date->format('d-m-Y h:i:s');
        //         },
        //         function ($dateAsString) {
        //             return new \DateTime()
        //         }
        //     ))
        // ;

        return $builder->getForm();
    }
}
