<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Martiis\GitlabCLI\Bag;
use Martiis\GitlabCLI\BagAwareInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

$app = new Application('Gitlab CLI', '0.1');
$parameters = Yaml::parse(__DIR__ . '/../app/parameters.yml')['parameters'];
$finder = Finder::create();

if ($parameters['gitlab_host'][strlen($parameters['gitlab_host']) - 1] !== '/') {
    $parameters['gitlab_host'] .= '/';
}

$parameters['guzzle'] = new \GuzzleHttp\Client(
    [
        'base_uri' => $parameters['gitlab_host'] . 'api/v3/',
        'query' => [
            'private_token' => $parameters['gitlab_token'],
        ]
    ]
);

$bag = new Bag($parameters);

/** @var SplFileInfo $file */
foreach ($finder->in(__DIR__ . '/../src/Command')->files() as $file) {
    $fname = pathinfo($file->getBasename(), PATHINFO_FILENAME);
    if (stripos($fname, 'Abstract') === false && stripos($fname, 'Interface') === false) {
        $ns = 'Martiis\\GitlabCLI\\Command\\' . $fname;
        $instance = new $ns();
        if ($instance instanceof BagAwareInterface) {
            $instance->setBag($bag);
        }
        $app->add($instance);
    }
}

$app->run();
