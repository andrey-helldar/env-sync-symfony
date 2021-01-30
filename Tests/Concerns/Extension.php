<?php

namespace Helldar\EnvSync\Frameworks\Symfony\Tests\Concerns;

use Helldar\EnvSync\Frameworks\Symfony\DependencyInjection\EnvSyncExtension;

final class Extension extends EnvSyncExtension
{
    protected function configPath(): string
    {
        return realpath(__DIR__ . '/../fixtures/config');
    }
}
