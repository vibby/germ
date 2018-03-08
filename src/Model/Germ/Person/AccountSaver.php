<?php

namespace Germ\Model\Germ\Person;

use Germ\Model\Germ\AbstractSaver;
use Germ\Model\Germ\PersonSchema\AccountModel;
use Germ\Model\Germ\PersonSchema\Account;
use Germ\Model\Germ\PersonSchema\PersonModel;
use Germ\Model\Germ\PersonSchema\Person;
use PommProject\Foundation\Pomm;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountSaver extends AbstractSaver
{
    private $personModel;

    public function __construct(
        Pomm $pomm,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
        $this->personModel = $pomm['germ']->getModel(PersonModel::class);
        $this->encoder = $encoder;
    }

    protected static function getModelClassName()
    {
        return AccountModel::class;
    }

    protected static function getEntityClassName()
    {
        return Account::class;
    }

    public function insertOrUpdate($accountData, Person $person)
    {
        if (isset($accountData['email'])) {
            $person['email'] = $accountData['email'];
            $this->personModel->updateOne($person, ['email']);
        } else {
            $accountData['email'] = $person['email'];
        }
        $account = self::buildEntity($accountData);
        $account['password'] = $this->encoder->encodePassword($account, $accountData['plainPassword']);
        unset($account['roles']);
        if ($account->has('id_person_account')) {
            $this->model->updateOne($account, ['password']);
        } else {
            $account['email_canonical'] = $person['email'];
            $account['person_id'] = $person['id_person_person'];
            $account['enabled'] = true;
            $this->model->insertOne($account);
        }

        return $account;
    }
}
