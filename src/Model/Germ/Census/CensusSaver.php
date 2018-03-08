<?php

namespace Germ\Model\Germ\Census;

use Germ\Model\Germ\AbstractSaver;
use Germ\Model\Germ\ChurchSchema\Census;
use Germ\Model\Germ\ChurchSchema\CensusModel;
use PommProject\Foundation\Pomm;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class CensusSaver extends AbstractSaver
{
    protected $model;
    private $finder;
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, CensusFinder $censusFinder, TokenStorage $tokenStorage, AuthorizationChecker $authorisationChecker)
    {
        parent::__construct($pomm);
        $this->user = $tokenStorage->getToken()->getUser();
        $this->authorisationChecker = $authorisationChecker;
        $this->finder = $censusFinder;
    }

    protected static function getModelClassName()
    {
        return CensusModel::class;
    }

    protected static function getEntityClassName()
    {
        return Census::class;
    }

    public function create($censusData)
    {
        $census = self::buildEntity($censusData);
        if (!$this->authorisationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $census->setChurchId($this->user->getChurchId());
        }
        $this->model->insertOne($census);

        return $census;
    }

    public function update(Census $census, array $fields = [])
    {
        if (in_array('email', $census->getModifiedColumns())) {
            $account = $this->finder->findAccountForCensus($census);
            $account->setEmailCanonical($census['email']);
            unset($account['roles']);
            $this->modelAccount->updateOne($account);
        }
        $this->model->updateOne($census, $fields);
    }
}
