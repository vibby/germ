<?php

namespace Germ\Model\Germ\Communication;

use Germ\Model\Germ\AbstractSaver;
use Germ\Model\Germ\CommunicationSchema\Sms;
use Germ\Model\Germ\CommunicationSchema\SmsModel;
use PommProject\Foundation\Pomm;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SmsSaver extends AbstractSaver
{
    private $finder;
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, SmsFinder $smsFinder, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorisationChecker)
    {
        parent::__construct($pomm);
        $this->user = $tokenStorage->getToken()->getUser();
        $this->authorisationChecker = $authorisationChecker;
        $this->finder = $smsFinder;
    }

    protected static function getModelClassName()
    {
        return SmsModel::class;
    }

    protected static function getEntityClassName()
    {
        return Sms::class;
    }

    public function create($smsData)
    {
        $sms = self::buildEntity($smsData);
        if (!$this->authorisationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $sms->setChurchId($this->user->getChurchId());
        }
        $this->model->insertOne($sms);

        return $sms;
    }

    public function update(Sms $sms, array $fields = [])
    {
        $this->model->updateOne($sms, $fields);
    }
}
