<?php

namespace GermBundle\Model\Germ\Census;

use GermBundle\Model\Germ\AbstractSaver;
use GermBundle\Model\Germ\ChurchSchema\Census;
use GermBundle\Model\Germ\ChurchSchema\CensusModel;
use PommProject\Foundation\Pomm;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CensusSaver extends AbstractSaver
{
    protected $model;
    private $finder;
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, CensusFinder $censusFinder, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorisationChecker)
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
