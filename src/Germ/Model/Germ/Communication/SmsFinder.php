<?php

namespace Germ\Model\Germ\Communication;

use Germ\Filter\FilterFinder;
use Germ\Model\Germ\AbstractFinder;
use Germ\Model\Germ\CommunicationSchema\SmsModel;
use Germ\Model\Germ\PersonSchema\AccountModel;
use Germ\Model\Germ\PersonSchema\Person;
use Germ\Model\Germ\PersonSchema\PersonModel;
use PommProject\Foundation\Pomm;
use PommProject\Foundation\Where;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SmsFinder extends AbstractFinder
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

    protected static function getModelClassName()
    {
        return SmsModel::class;
    }

    public function alterWhere(Where $where)
    {
        if (! $this->authorisationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $where->andWhere('church_id = $*', [$this->user['church_id']]);
        }

        return $where;
    }

    public function findOneByDate(\DateTime $date)
    {
        return $this->findWhere(new Where('date = $1', [':date' => $date->format('Y-m-d H:i:s.u')]))->current();
    }
}
