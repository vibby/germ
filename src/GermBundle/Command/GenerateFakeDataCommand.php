<?php

namespace GermBundle\Command;

use Faker\Factory;
use GermBundle\Model\Germ\PersonSchema\Account;
use GermBundle\Model\Germ\PersonSchema\AccountModel;
use GermBundle\Model\Germ\PersonSchema\Person;
use GermBundle\Model\Germ\PersonSchema\PersonModel;
use PragmaFabrik\Pomm\Faker\FakerPooler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GenerateFakeDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('germ:generate_fake_data')
            ->setDescription('Generate many data for dev and tests. Not allowed on production')
            ->setHelp('This command allows you to generate many data : churches and persons. For testing and work with data for developing without real data.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pommSession = $this->getContainer()->get('pomm')['germ'];
        $pommSession->registerClientPooler(new FakerPooler(Factory::create()));

        $pommSession->getFaker('church.church')->getRowDefinition()
            ->unsetDefinition('id_church_church')
            ->setFormatterType('name', 'city')
            ->setFormatterType('phone', 'phoneNumber')
        ;
        $churches = $pommSession->getFaker('church.church')->save(93);
        $churchIds = array_map(
            function ($church) { return $church['id_church_church'];},
            $churches
        );

        $pommSession->getFaker('person.person')->getRowDefinition()
            ->unsetDefinition('id_person_person')
            ->unsetDefinition('is_deleted')
            ->setFormatterType('firstname', 'firstName')
            ->setFormatterType('lastname', 'lastName')
            ->setFormatterType('birthdate', 'dateTime', ['18 years ago'])
            ->setFormatterType(
                'roles',
                'randomElement',
                [[
                    ['ROLE_ELDER'],
                    ['ROLE_ELDER', 'ROLE_TREASURER'],
                    ['ROLE_DEACON'],
                    ['ROLE_DEACON'],
                    ['ROLE_DEACONESS'],
                    ['ROLE_DEACONESS'],
                    ['ROLE_SECRETARY'],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                ]]
            )
            ->setFormatterType('email', 'email')
            ->setFormatterType('church_id', 'randomElement', [$churchIds])
        ;

        $persons = $pommSession->getFaker('person.person')->save(1219);

        $accountModel = $pommSession->getModel(AccountModel::class);
        foreach ($persons as $person) {
            $account = new Account();
            $account->setEnabled(true);
            $account->setUsername($person['slug_canonical']);
            $account->setUsernameCanonical($person['slug_canonical']);
            $account->setEmail($person['email']);
            $account->setEmailCanonical($person['email']);
            // password is «test»
            $account->setPassword('LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==');
            $account->setSalt('5cmq15n0q0w0go4ogcc0co444ocw4oc');
            $account->setPersonId($person['id_person_person']);
            $accountModel->insertOne($account);
            unset($account);
        }

        $personModel = $pommSession->getModel(PersonModel::class);
        $adminPerson = new Person();
        $adminPerson->setFirstname('Admin')
            ->setLastname('Testeur')
            ->setEmail('admin@germ.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setChurchId($churchIds[0])
        ;
        $personModel->insertOne($adminPerson);

        $accountModel = $pommSession->getModel(AccountModel::class);
        $adminAccount = new Account();
        $adminAccount->setEnabled(true);
        $adminAccount->setUsername('Admin');
        $adminAccount->setUsernameCanonical('Admin');
        $adminAccount->setEmail('admin@germ.fr');
        $adminAccount->setEmailCanonical('admin@germ.fr');
        // password is «test»
        $adminAccount->setPassword('LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==');
        $adminAccount->setSalt('5cmq15n0q0w0go4ogcc0co444ocw4oc');
        $adminAccount->setPersonId($adminPerson['id_person_person']);
        $accountModel->insertOne($adminAccount);
    }
}
