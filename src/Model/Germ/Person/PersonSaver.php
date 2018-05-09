<?php

namespace Germ\Model\Germ\Person;

use Germ\Model\Germ\AbstractSaver;
use Germ\Model\Germ\PersonSchema\AccountModel;
use Germ\Model\Germ\PersonSchema\Person;
use Germ\Model\Germ\PersonSchema\PersonModel;
use PommProject\Foundation\Pomm;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PersonSaver extends AbstractSaver
{
    protected $model;
    protected $modelAccount;
    private $finder;
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, PersonFinder $personFinder, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorisationChecker)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
        $this->modelAccount = $pomm['germ']->getModel(AccountModel::class);
        $this->user = $tokenStorage->getToken()->getUser();
        $this->authorisationChecker = $authorisationChecker;
        $this->finder = $personFinder;
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

    public function update(Person $person, array $fields = [])
    {
        if (in_array('email', $person->getModifiedColumns())) {
            $account = $this->finder->findAccountForPerson($person);
            $account->setEmailCanonical($person['email']);
            unset($account['roles']);
            $this->modelAccount->updateOne($account);
        }
        $this->model->updateOne($person, $fields);
    }
}
