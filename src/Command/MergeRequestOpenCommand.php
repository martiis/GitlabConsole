<?php

namespace Martiis\GitlabCLI\Command;

use Doctrine\Common\Cache\FilesystemCache;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MergeRequestOpenCommand extends AbstractProjectAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('merge-request:open')
            ->setDescription('Opens merge request.')
            ->addArgument(
                'target',
                InputArgument::REQUIRED,
                'Branch to merge from.'
            )
            ->addArgument(
                'head',
                InputArgument::REQUIRED,
                'Branch to merge to'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FilesystemCache $cache */
        $cache = $this->getBag()->get('cache');
        if (!$cache->contains($input->getArgument('project'))) {
            throw new \LogicException('Project namespace not found!');
        }

//        /** @var ClientInterface $client */
//        $client = $this->getBag()->get('guzzle');
//        $response = json_decode($client->request('GET', 'projects')->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);

        $io = $this->getIO($input, $output);
        foreach (['target' => 'Target branch', 'head' => 'Head branch'] as $name => $qstn) {
            $input->getArgument($name) === null && $input->setArgument($name, $io->ask($qstn));
        }
    }
}
