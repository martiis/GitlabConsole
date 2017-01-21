<?php

namespace Martiis\GitlabCLI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractProjectAwareCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->addArgument(
                'project',
                InputArgument::REQUIRED,
                'Project namespace in gitlab. f.e. team/project'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);

        if ($input->getArgument('project') === null) {
            $input->setArgument(
                'project',
                $this->getIO($input, $output)->ask(
                    'Project (f.e. team/project)',
                    null,
                    function ($value) {
                        if (!preg_match('/^[\w\d]+\/[\w\d]+$/i', $value)) {
                            throw new \LogicException('Invalid project.');
                        }

                        return $value;
                    }
                )
            );
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return SymfonyStyle
     */
    public function getIO(InputInterface $input, OutputInterface $output)
    {
        if (!$this->io) {
            $this->io = new SymfonyStyle($input, $output);
        }

        return $this->io;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
