<?php

namespace Germ\Legacy\Command;

use PommProject\Foundation\Pomm;
use PommProject\Foundation\Session;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RemoveAllDataCommand extends Command
{
    private $pommSession;
    private $params;

    public function __construct(Pomm $pomm, ParameterBagInterface $params)
    {
        parent::__construct(self::class);
        $this->pommSession = $pomm['germ'];
        $this->params = $params;
    }

    protected function configure()
    {
        $this
            ->setName('germ:remove-all-data')
            ->setDescription('Remove all existing data')
            ->setHelp('Remove churches and persons. Not allowed on production. For testing and work with data for developing without real data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('prod' == $this->params->get('kernel.environment')) {
            throw new \Exception('This command cannot be executed on PROD environment', 1);
        }

        $output->write('Removing all data in database : ');

        /* @var Session $pommSession */
        $this->pommSession->getConnection()->executeAnonymousQuery(
            <<<SQL
            DELETE FROM event.assignation;
            DELETE FROM event.docket;
            DELETE FROM event.event;
            DELETE FROM event.event_type;
            DELETE FROM event.location;
            DELETE FROM person.account;
            DELETE FROM person.person_church;
            DELETE FROM person.person;
            DELETE FROM church.census;
            DELETE FROM church.church;
SQL
        );

        $output->writeln('Done');
    }
}
