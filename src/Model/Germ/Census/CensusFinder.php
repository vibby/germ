<?php

namespace Germ\Model\Germ\Census;

use Germ\Filter\FilterFinder;
use Germ\Model\Germ\AbstractFinder;
use Germ\Model\Germ\ChurchSchema\CensusModel;
use PommProject\Foundation\Pomm;
use PommProject\Foundation\Where;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class CensusFinder extends AbstractFinder
{
    use FilterFinder;

    protected $model;
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, TokenStorage $tokenStorage, AuthorizationChecker $authorisationChecker)
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
