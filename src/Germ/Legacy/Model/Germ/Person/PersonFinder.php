<?php

namespace Germ\Legacy\Model\Germ\Person;

use Germ\Legacy\Filter\FilterFinder;
use Germ\Legacy\Model\Germ\AbstractFinder;
use Germ\Legacy\Model\Germ\PersonSchema\AccountModel;
use Germ\Legacy\Model\Germ\PersonSchema\Person;
use Germ\Legacy\Model\Germ\PersonSchema\PersonModel;
use PommProject\Foundation\Pomm;
use PommProject\Foundation\Where;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PersonFinder extends AbstractFinder
{
    use FilterFinder;

    protected $model;
    protected $modelAccount;
    private $user;
    private $authorisationChecker;

    public function __construct(Pomm $pomm, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorisationChecker)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
        $this->modelAccount = $pomm['germ']->getModel(AccountModel::class);
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
        if (! $this->authorisationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $where->andWhere('church_id = $*', [$this->user['church_id']]);
        }
        if (! $this->authorisationChecker->isGranted('ROLE_PERSON_DELETED')) {
            $where->andWhere('is_deleted = $*', [0]);
        }

        return $where;
    }

    public function findByRole(array $role)
    {
        $where = new Where();
        $where->andWhereIn('role', $role);
        $where = $this->alterWhere($where);

        return $this->findWhere($where);
    }

    public function findForListWhere(Where $where = null)
    {
        if (! $where) {
            $where = new Where();
        }
        $where = $this->alterWhere($where);

        return $this->model->findForListWhere($where);
    }

    public function findAccountForPerson(Person $person)
    {
        $where = new Where('person_id = $*', [':person_id' => $person->getId()]);
        $where = $this->alterWhere($where);

        return $this->modelAccount->findWhere($where)->current();
    }
}
