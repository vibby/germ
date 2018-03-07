<?php

namespace GermBundle\Command;

use Faker\Factory;
use GermBundle\Model\Germ\ChurchSchema\Church;
use GermBundle\Model\Germ\ChurchSchema\ChurchModel;
use GermBundle\Model\Germ\PersonSchema\Account;
use GermBundle\Model\Germ\PersonSchema\AccountModel;
use GermBundle\Model\Germ\PersonSchema\Person;
use GermBundle\Model\Germ\PersonSchema\PersonModel;
use PragmaFabrik\Pomm\Faker\FakerPooler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GenerateFakeDataCommand extends ContainerAwareCommand
{
    private $pommSession;
    private $accountModel;

    protected function configure()
    {
        $this
            ->setName('germ:generate-fake-data')
            ->setDescription('Generate many data for dev and tests. Not allowed on production')
            ->setHelp('This command allows you to generate many data : churches and persons. For testing and work with data for developing without real data.')
            ->addOption('persons', 'p', InputOption::VALUE_REQUIRED, 'Number of person to generate', 1082)
            ->addOption('churches', 'c', InputOption::VALUE_REQUIRED, 'Number of churches to generate', 14)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getContainer()->getParameter("kernel.environment") == 'prod') {
            throw new \Exception("This command cannot be executed on PROD environment", 1);
        }
        $this->output = $output;
        $output->writeln('Generating fixed data in database : ');

        $this->pommSession = $this->getContainer()->get('pomm')['germ'];
        $this->accountModel = $this->pommSession->getModel(AccountModel::class);

        // Fix data
        $churchModel = $this->pommSession->getModel(ChurchModel::class);
        if (!($church = $churchModel->findWhere('name = $*', ['Nantes'])->next())) {
            $church = new Church();
            $church->setName('Nantes');
            $churchModel->insertOne($church);
        }
        $this->createPerson('Test', 'Admin', $church, 'admin@germ.fr', ['ROLE_ADMIN']);
        $this->createPerson('Test', 'Fédération', $church, 'federation@germ.fr', ['ROLE_FEDERATION']);
        $this->createPerson('Test', 'Secrétaire', $church, 'secretaire@germ.fr', ['ROLE_SECRETARY']);
        $this->createPerson('Test', 'Directeur', $church, 'directeur@germ.fr', ['ROLE_DEPT_DIRECTOR']);
        $this->createPerson('Test', 'Membre', $church, 'membre@germ.fr');

        // Generated data
        $output->writeln('Generating random data in database : ');
        $this->pommSession->registerClientPooler(new FakerPooler(Factory::create('fr_FR')));
        $this->defineChurchFaker();
        $churches = $this->pommSession->getFaker('church.church')->save((int) $input->getOption('churches'));

        $churchIds = array_map(
            function ($church) { return $church['id_church_church'];},
            $churches
        );
        $this->definePersonFaker($churchIds);
        $persons = $this->pommSession->getFaker('person.person')->save((int) $input->getOption('persons'));
        foreach ($persons as $person) {
            $this->createAssociatedAccount($person);
        }
        $this->defineCensusFaker($churchIds);
        $this->pommSession->getFaker('church.census')->save(((int) $input->getOption('churches'))*100);
        $output->writeln('Done');
    }

    private function createAssociatedAccount($person)
    {
        $account = new Account();
        $account->setEnabled(true);
        // password is «test»
        $account->setPassword('LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==');
        $account->setSalt('5cmq15n0q0w0go4ogcc0co444ocw4oc');
        $account->setEmailCanonical($person['email']);
        $account->setPersonId($person['id_person_person']);
        $this->accountModel->insertOne($account);
        unset($account);
    }

    private function defineChurchFaker()
    {
        $this->pommSession->getFaker('church.church')->getRowDefinition()
            ->unsetDefinition('id_church_church')
            ->setFormatterType('name', 'city')
            ->setFormatterType('phone', 'phoneNumber')
            ->setDefinition('latlong', function(\Faker\Generator $generator) { return '('.$generator->longitude(45, 50).', '.$generator->latitude(0, 5).')'; })
            ->setDefinition('address', function(\Faker\Generator $generator) { return $generator->streetAddress()."\n".$generator->postcode().' '.$generator->city(); })
        ;
    }

    private function defineCensusFaker($churchIds)
    {
        $this->pommSession->getFaker('church.census')->getRowDefinition()
            ->unsetDefinition('id_church_census')
            ->setFormatterType('church_id', 'randomElement', [$churchIds])
            ->setFormatterType('date', 'dateTime')
            ->setFormatterType('count', 'numberBetween', [40, 300])
        ;
    }

    private function definePersonFaker($churchIds)
    {
        $this->pommSession->getFaker('person.person')->getRowDefinition()
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
                    ['ROLE_ELDER', 'ROLE_DEPT_DIRECTOR'],
                    ['ROLE_TREASURER'],
                    ['ROLE_SECRETARY'],
                    ['ROLE_DEPT_DIRECTOR'],
                    ['ROLE_FIRST_DEACONESS'],
                    ['ROLE_FIRST_DEACON'],
                    ['ROLE_DEACON'],
                    ['ROLE_DEACON'],
                    ['ROLE_DEACON'],
                    ['ROLE_DEACONESS'],
                    ['ROLE_DEACONESS'],
                    ['ROLE_DEACONESS'],
                    ['ROLE_DEACONESS'],
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
                    [],
                ]]
            )
            ->setFormatterType('email', 'email')
            ->setFormatterType('church_id', 'randomElement', [$churchIds])
            ->setDefinition('latlong', function(\Faker\Generator $generator) {
                return '('.$generator->longitude(45, 50).', '.$generator->latitude(0, 5).')';
            })
            ->setDefinition('address', function(\Faker\Generator $generator) {
                return $generator->streetAddress()."\n".$generator->postcode().' '.$generator->city();
            })
            ->setDefinition('phone', function(\Faker\Generator $generator) {
                return [$generator->phoneNumber()];
            })
        ;
    }

    private function createPerson($lastname, $firstname, Church $church, $email = null, $role = [])
    {
        $pommSession = $this->getContainer()->get('pomm')['germ'];
        $personModel = $pommSession->getModel(PersonModel::class);
        if (!$personModel->findWhere('email = $*', [$email])->count()) {
            $person = new Person();
            $person->setFirstname($firstname)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setRoles($role)
                ->setChurchId($church['id_church_church']);
            $personModel->insertOne($person);
            $this->createAssociatedAccount($person);
            $this->output->writeln(sprintf('  - %s created with password «%s»', $person->getEmail(), 'test'));
        }
    }
}
