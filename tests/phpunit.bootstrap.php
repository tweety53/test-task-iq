<?php

require_once __DIR__ . '/../vendor/autoload.php';

function bootstrap(): void
{
    echo 'Bootstrapping db.....' . PHP_EOL;

    $kernel = new \App\Kernel('test', true);
    $kernel->boot();

    $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    $application->setAutoExit(false);

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => '1',
        '--force' => '1',
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:create',
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:migrations:migrate',
        '--no-interaction' => '1',
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '--no-interaction' => '1',
    ]));

    $kernel->shutdown();
}

bootstrap();