<?php

namespace GermBundle\Model\Germ\PersonSchema;

use GermBundle\Filter\FilterFinder;
use GermBundle\Model\Germ\AbstractFinder;
use PommProject\Foundation\Pomm;
use PommProject\Foundation\Where;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class PersonFinder extends AbstractFinder
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

    protected static function getModelClassName()
    {
        return PersonModel::class;
    }

    public function alterWhere(Where $where, $withActive = true, $withDeleted = false)
    {
        if (!$this->authorisationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $where->andWhere('church_id = $*', [$this->user['church_id']]);
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
    public function findOneBySlug($slug)
    {
        $where = new Where('slug_canonical = $1', [':slug' => $slug]);
        $where = $this->alterWhere($where);

        return $this->model->findWhere($where)->current();
    }
}
