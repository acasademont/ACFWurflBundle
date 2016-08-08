<?php

/*
 * This file is part of the ACFWurflBundle.
 *
 * (c) Albert Casademont <albertcasademont@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACF\Bundle\WurflBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Build the WURFL repository from the xml file
 *
 */
class WurflRepositoryBuildCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('wurfl:repository:build')
            ->setDescription('Build the WURFL repository from the xml file')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        //Empty the current repository dir
        $persistence = $container->get('acf_wurfl.container')->persistence;
        $repositoryDir = $persistence['params']['dir'];
        if (is_dir($repositoryDir)) {
            $output->writeln('<info>Clearing the old repository</info>');
            if (!is_writable($repositoryDir)) {
                throw new \RuntimeException(sprintf('Unable to write in the "%s" directory', $repositoryDir));
            }
            $container->get('filesystem')->remove($repositoryDir);
        }
        //Build the new one
        $output->writeln('<info>Building the new repository</info>');
        $wurfl = $container->get('wurfl');
    }
}
