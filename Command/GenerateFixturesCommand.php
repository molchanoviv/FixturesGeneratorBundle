<?php
/**
 * This file is part of ONP.
 *
 * Copyright (c) 2014 Opensoft (http://opensoftdev.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Opensoft is prohibited.
 */
 
namespace Opensoft\FixturesGeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Opensoft\FixturesGeneratorBundle\Generator\Generator;

/**
 * Opensoft\FixturesGeneratorBundle\Command\GenerateFixturesCommand
 *
 * @author Ivan Molchanov <ivan.molchanov@opensoftdev.ru>
 */
class GenerateFixturesCommand extends ContainerAwareCommand
{
    /**
     * config command
     */
    public function configure()
    {
        $this->setName('opensoft:fixtures:generate');
        $this->setDescription("Generate product related fixture files by connecting directly to a name connection");
        $this->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'Connection name', 'default');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = $this->getGenerator();
        $generator->generate();
        $output->writeln('<comment>Done</comment>');

        return null;
    }

    /**
     * @return Generator
     */
    protected function getGenerator()
    {
        return $this->getContainer()->get('opensoft.fixtures_generator');
    }
}
