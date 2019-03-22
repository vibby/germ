<?php

namespace Germ\Command;

use PommProject\Foundation\Session;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveAllDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('germ:remove-all-data')
            ->setDescription('Remove all existing data')
            ->setHelp('Remove churches and persons. Not allowed on production. For testing and work with data for developing without real data.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('prod' == $this->getContainer()->getParameter('kernel.environment')) {
            throw new \Exception('This command cannot be executed on PROD environment', 1);
        }

        $output->write('Removing all data in database : ');

        /** @var Session $pommSession */
        $pommSession = $this->getContainer()->get('pomm')['germ'];
        $pommSession->getConnection()->executeAnonymousQuery(
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
