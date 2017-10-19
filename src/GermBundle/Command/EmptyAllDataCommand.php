<?php

namespace GermBundle\Command;

use PommProject\Foundation\Session;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class EmptyAllDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('germ:empty_all_data')
            ->setDescription('Remove all existing data')
            ->setHelp('Remove churches and persons. Not allowed on production. For testing and work with data for developing without real data.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Session $pommSession */
        $pommSession = $this->getContainer()->get('pomm')['germ'];
        $pommSession->getConnection()->executeAnonymousQuery(<<<SQL
            DELETE FROM person.account;
            DELETE FROM person.person_church;
            DELETE FROM person.person;
            DELETE FROM church.church;
SQL
        );

        $output->writeln('Done');
    }
}
