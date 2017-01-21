<?php

namespace Martiis\GitlabCLI\Command;

use Doctrine\Common\Cache\FilesystemCache;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MergeRequstListCommand extends AbstractProjectAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('merge-request:list')
            ->setDescription('Display\'s a list of merge requests.')
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Includes closed merge requests.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var FilesystemCache $cache */
        $cache = $this->container->get('cache');
        if (!$cache->contains($input->getArgument('project'))) {
            throw new \LogicException('Project namespace not found! Try to clear cache.');
        }

        $id = $cache->fetch($input->getArgument('project'));
        $state = $input->getOption('all') ? 'all' : 'opened';
        /** @var ClientInterface $client */
        $client = $this->container->get('guzzle');
        $response = $client->request(
            'GET',
            sprintf('projects/%s/merge_requests', $id),
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'query' => array_merge(
                    $client->getConfig('query'),
                    [
                        'state' => $state
                    ]
                )
            ]
        );

        $rows = [];
        $encoded = json_decode($response->getBody()->getContents(), true);

        foreach ($encoded as $item) {
            $rows[] = [
                $item['iid'],
                $item['title'],
                empty($item['author']['name']) ? $item['author']['username'] : $item['author']['name'],
                $item['source_branch'],
                $item['target_branch'],
                date('y-m-d H:i', strtotime($item['created_at'])),
                date('y-m-d H:i', strtotime($item['updated_at'])),
                $item['state'],
                isset($item['merge_status']) ? $item['merge_status'] : '',
            ];
        }

        $this
            ->getIO($input, $output)
            ->table(
                ['#', 'Title', 'Author', 'Source', 'Target', 'Created', 'Updated', 'State', 'Merge status'],
                $rows
            );
    }
}
