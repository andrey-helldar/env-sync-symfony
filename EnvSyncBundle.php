<?php

namespace Helldar\EnvSync\Frameworks\Symfony;

use Composer\Config;
use Helldar\EnvSync\Frameworks\Symfony\Command\Sync;
use Helldar\EnvSync\Frameworks\Symfony\DependencyInjection\EnvSyncExtension;
use Helldar\EnvSync\Services\Syncer;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnvSyncBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $command = $this->getCommand($application);

        $application->add($command);
    }

    protected function createContainerExtension(): EnvSyncExtension
    {
        return new EnvSyncExtension();
    }

    protected function getCommand(Application $application): Sync
    {
        return new Sync($this->composerConfig(), $this->getSyncer($application));
    }

    protected function composerConfig(): Config
    {
        return new Config();
    }

    protected function getSyncer(Application $application): Syncer
    {
        $config = $this->getConfig($application);

        return Syncer::make($config);
    }

    protected function getConfig(Application $application): array
    {
        return $application->get('container')->getParameter('forces');
    }
}
