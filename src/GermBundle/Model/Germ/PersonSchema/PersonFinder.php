<?php

namespace GermBundle\Model\Germ\PersonSchema;

use GermBundle\Filter\FilterFinder;
use GermBundle\Model\Germ\AbstractFinder;
use PommProject\Foundation\Pomm;
use PommProject\Foundation\Where;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PersonFinder extends AbstractFinder
{
    use FilterFinder;

    protected $model;
    private $user;

    public function __construct(Pomm $pomm, TokenStorage $tokenStorage)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
        $this->user = $tokenStorage->getToken()->getUser();
    }

    protected static function getModelClassName()
    {
        return PersonModel::class;
    }

    public function alterWhere(Where $where, $limitToUserChurch = true, $withActive = true, $withDeleted = false)
    {
        if ($limitToUserChurch) {
//            $where->andWhere('church = ', $this->user->getPerson()->getChurch());
        }
        if (!$withActive && !$withDeleted) {
            throw new \Exception('Cannot ask for deleted and undeleted persons');
        }
        if ($withActive && !$withActive) {
            $where->andWhere('is_deleted = true');
        }
        if (!$withActive && $withActive) {
            $where->andWhere('is_deleted = false');
        }

        return $where;
    }

    public function findByRole(Array $role)
    {
        $where = new Where();
        if ($role) {
            $where->andWhereIn('role', $role);
        }
        return $this->findWhere($where);
    }
}
