<?php

namespace Martiis\GitlabCLI\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CommandCompiler implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $finder = Finder::create();
        $rootDir = $container->getParameter('root_dir');

        /** @var SplFileInfo $file */
        foreach ($finder->in($rootDir . '/src/Command')->files() as $file) {
            $fname = pathinfo($file->getBasename(), PATHINFO_FILENAME);
            if (stripos($fname, 'Abstract') === false && stripos($fname, 'Interface') === false) {
                $definition = new Definition('Martiis\\GitlabCLI\\Command\\' . $fname);
                $definition->addMethodCall('setContainer', [new Reference('container')]);

                $container->getDefinition('app')->addMethodCall('add', [$definition]);
            }
        }
    }
}