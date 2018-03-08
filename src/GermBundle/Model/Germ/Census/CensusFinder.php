<?php

namespace GermBundle\Model\Germ\Census;

use GermBundle\Filter\FilterFinder;
use GermBundle\Model\Germ\AbstractFinder;
use GermBundle\Model\Germ\ChurchSchema\CensusModel;
use PommProject\Foundation\Pomm;
use PommProject\Foundation\Where;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CensusFinder extends AbstractFinder
{
    use FilterFinder;

    protected $model;
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorisationChecker)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
        $this->user = $tokenStorage->getToken()->getUser();
        $this->authorisationChecker = $authorisationChecker;
    }

    public function getDefaultOrderBy()
    {
        return ['date'];
    }

    public function alterWhere(Where $where)
    {
        if (!$this->authorisationChecker->isGranted('ROLE_CHURCH_READ')) {
            $where->andWhere('church_id = $*', [$this->user['church_id']]);
        }

        return $where;
    }

    protected static function getModelClassName()
    {
        return CensusModel::class;
    }
}
