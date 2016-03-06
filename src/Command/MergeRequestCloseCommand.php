<?php

/**
 * @copyright C UAB NFQ Technologies 2015
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 */

namespace Martiis\GitlabCLI\Command;

use Doctrine\Common\Cache\FilesystemCache;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MergeRequestCloseCommand extends AbstractProjectAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('merge-request:close')
            ->setDescription('Closes a merge request.')
            ->addArgument(
                'iid',
                InputArgument::REQUIRED,
                'Merge request iid'
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
            throw new \LogicException('Project namespace not found! Try to clear cache.');
        }

        $id = $cache->fetch($input->getArgument('project'));
        /** @var ClientInterface $client */
        $client = $this->getBag()->get('guzzle');
        $response = $client->request(
            'GET',
            sprintf('projects/%s/merge_requests', $id),
            [
                'query' => array_merge(
                    $client->getConfig('query'),
                    [
                        'iid' => $input->getArgument('iid')
                    ]
                )
            ]
        );

        $encoded = json_decode($response->getBody()->getContents(), true);
        if (empty($encoded)) {
            throw new \LogicException('Merge-request does not exist.');
        }

        $encoded = $encoded[0];

        if ($encoded['state'] === 'closed') {
            throw new \LogicException('Merge-request is already closed.');
        }

        $client->request(
            'PUT',
            sprintf('projects/%s/merge_requests/%s', $id, $encoded['id']),
            [
                'body' => json_encode(
                    [
                        'id' => $id,
                        'merge_request_id' => $encoded['id'],
                        'state_event' => 'close',
                    ]
                )
            ]
        );

        $this
            ->getIO($input, $output)
            ->success(sprintf('Merge-request #%s successfully closed.', $input->getArgument('iid')));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);

        !$input->hasArgument('idd') && $input->setArgument(
            'iid',
            $this->getIO($input, $output)->ask('Merge-request id')
        );
    }
}
