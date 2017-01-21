<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Martiis\GitlabCLI\Compiler\HostTrailCompiler;

$container = new ContainerBuilder();
$container->setParameter('root_dir', dirname(__DIR__));

$container
    ->register('app', Application::class)
    ->setArguments(['Gitlab CLI', '0.2']);

$loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/app'));
$loader->load('parameters.yml');
$loader->load('services.yml');

$container->addCompilerPass(new HostTrailCompiler());
$container->compile();

$container->get('app')->run();
