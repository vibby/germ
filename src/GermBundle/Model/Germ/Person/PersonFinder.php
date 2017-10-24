<?php

namespace GermBundle\Model\Germ\Person;

use GermBundle\Filter\FilterFinder;
use GermBundle\Model\Germ\AbstractFinder;
use GermBundle\Model\Germ\PersonSchema\PersonModel;
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

    public function getDefaultOrderBy()
    {
        return ['lastname', 'firstname'];
    }

    protected static function getModelClassName()
    {
        return PersonModel::class;
    }

    public function alterWhere(Where $where)
    {
        if (!$this->authorisationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $where->andWhere('church_id = $*', [$this->user['church_id']]);
        }
        if (!$this->authorisationChecker->isGranted('ROLE_PERSON_DELETED')) {
            $where->andWhere('is_deleted = $*', [0]);
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
        $where = new Where('slug = $*', [':slug' => $slug]);
        $where = $this->alterWhere($where);

        return $this->model->findWhere($where)->current();
    }

    public function findForListWhere(Where $where = null)
    {
        if (!$where) {
            $where = new Where();
        }
        $where = $this->alterWhere($where);

        return $this->model->findForListWhere($where);
    }
}
