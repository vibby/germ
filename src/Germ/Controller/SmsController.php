<?php

namespace Germ\Controller;

use Germ\Filter\Sms\Searcher;
use Germ\Model\Germ\Communication\SmsFinder;
use Germ\Model\Germ\Communication\SmsSaver;
use Germ\Model\Germ\CommunicationSchema\Sms;
use Germ\Model\Germ\CommunicationSchema\SmsModel;
use Germ\Type\SmsType;
use Knp\Component\Pager\PaginatorInterface;
use PommProject\Foundation\Pomm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class SmsController extends AbstractController
{
    const DATE_URL_FORMAT = 'Y-m-d-H-i-s-u';

    private $finder;
    private $searcher;
    private $saver;
    private $smsSaver;
    private $smsModel;
    private $paginator;

    public function __construct(SmsFinder $finder, Searcher $searcher, SmsSaver $saver, Pomm $pomm, PaginatorInterface $paginator)
    {
        $this->finder = $finder;
        $this->searcher = $searcher;
        $this->saver = $saver;
        $this->smsSaver = $saver;
        $this->smsModel = $pomm['germ']->getModel(SmsModel::class);
        $this->paginator = $paginator;
    }

    public function listAction(Request $request, $page)
    {
        if ('html' != $request->get('_format')) {
            $output['sms'] = $this->finder->findForListWhere();
        } else {
            if ($redirect = $this->searcher->handleRequest($request)) {
                return $redirect;
            }
            $output['searchForm'] = $this->searcher->getForm()->createView();
            $output['paginatedSms'] = $this->paginator->paginate(
                [
                    $this->finder,
                    'paginateFilterQuery',
                    [$this->searcher],
                ],
                $page,
                min((int) $request->get('perPage', 30), 250)
            );
        }

        $response = $this->render('Sms/list.'.$request->get('_format').'.twig', $output);

        if ('html' != $request->get('_format')) {
            $response->headers->set(
                'Content-Disposition',
                sprintf('attachment; filename="sms.%s";"', $request->get('_format'))
            );
            $response->headers->set(
                'Content-Type',
                sprintf('Content-Type="text/%s";', $request->get('_format'))
            );
        }

        return $response;
    }

    public function createAction(Request $request, SmsSaver $saver, TranslatorInterface $translator)
    {
        $smsForm = $this->createForm(SmsType::class);
        $smsForm->handleRequest($request);

        if ($smsForm->isSubmitted() && $smsForm->isValid()) {
            $sms = $saver->create($smsForm->getData());
            $request->getSession()->getFlashBag()->add('success', $translator->trans('Sms created'));

            return $this->redirectToRoute('germ_sms_edit', ['date' => $sms->getDate()->format(self::DATE_URL_FORMAT)]);
        }

        return $this->render(
            'Sms/edit.html.twig',
            [
                'mode' => 'create',
                'form' => $smsForm->createView(),
            ]
        );
    }

    public function editAction(Request $request, $date)
    {
        $sms = $this->getSmsOr404($date);
        $smsForm = $this->createForm(SmsType::class, $sms);
        $smsForm->handleRequest($request);
        if ($smsForm->isSubmitted() && $smsForm->isValid()) {
            $this->model->updateOne($sms, array_keys($smsForm->getData()->extract()));
            $request->getSession()->getFlashBag()->add('success', $this->translator->trans('Sms updated'));

            return $this->redirectToRoute('germ_sms_edit', ['smsSlug' => $sms->getSlug()]);
        }

        return $this->render(
            sprintf(
                'Sms/edit.%s.twig',
                $request->get('_format')
            ),
            [
                'mode' => 'edit',
                'form' => $smsForm->createView(),
                'sms' => $sms,
            ]
        );
    }

    private function getSmsOr404($date): Sms
    {
        $sms = $this->finder->findOneByDate(\DateTime::createFromFormat(self::DATE_URL_FORMAT, $date));
        if (! $sms) {
            throw $this->createNotFoundException('The sms does not exist');
        }

        return $sms;
    }
}
