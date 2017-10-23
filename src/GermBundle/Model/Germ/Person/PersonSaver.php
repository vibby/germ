<?php

namespace GermBundle\Model\Germ\Person;

use GermBundle\Model\Germ\AbstractSaver;
use GermBundle\Model\Germ\PersonSchema\Person;
use GermBundle\Model\Germ\PersonSchema\PersonModel;
use PommProject\Foundation\Pomm;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class PersonSaver extends AbstractSaver
{
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, TokenStorage $tokenStorage, AuthorizationChecker $authorisationChecker)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
        $this->user = $tokenStorage->getToken()->getUser();
        $this->authorisationChecker = $authorisationChecker;
    }

    protected static function getModelClassName()
    {
        return PersonModel::class;
    }

    protected static function getEntityClassName()
    {
        return Person::class;
    }

    public function create($personData)
    {
        $person = self::buildEntity($personData);
        if (!$this->authorisationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $person->setChurchId($this->user->getChurchId());
        }
        $this->model->insertOne($person);

        return $person;
    }
}
