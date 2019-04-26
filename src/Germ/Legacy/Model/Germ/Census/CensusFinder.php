<?php

namespace Germ\Legacy\Model\Germ\Census;

use Germ\Legacy\Filter\FilterFinder;
use Germ\Legacy\Model\Germ\AbstractFinder;
use Germ\Legacy\Model\Germ\ChurchSchema\CensusModel;
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

    public function findOneById($id)
    {
        return $this->findWhere(new Where('id_church_census = $1', [':id' => $id]))->current();
    }
}
