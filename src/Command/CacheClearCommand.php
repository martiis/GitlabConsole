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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends AbstractProjectAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clears out cache if exists and loads up new one.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getIO($input, $output)->comment('Clearing out cache...');

        /** @var FilesystemCache $cache */
        $cache = $this->getBag()->get('cache');
        $cache->flushAll();

        /** @var ClientInterface $client */
        $client = $this->getBag()->get('guzzle');
        $response = json_decode($client->request('GET', 'projects')->getBody()->getContents(), true);

        foreach ($response as $project) {
            if ($cache->save(stripslashes($project['path_with_namespace']), $project['id']) === false) {
                throw new \LogicException('Failed to into write cache.');
            }
        }

        $this->getIO($input, $output)->success('Cache has successfully been cleared out, and preloaded.');
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // to nothing.
    }
}
