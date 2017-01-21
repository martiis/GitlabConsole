<?php

namespace  Martiis\GitlabCLI\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HostTrailCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $host = $container->getParameter('gitlab_host');

        if ($host[strlen($host) - 1] !== '/') {
            $host .= '/';
            $container->setParameter('gitlab_host', $host);
        }
    }
}
