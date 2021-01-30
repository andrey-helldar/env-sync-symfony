<?php

namespace Helldar\EnvSync\Frameworks\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

class EnvSyncExtension extends BaseExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->loader(XmlFileLoader::class, $container)->load('services.xml');
        $this->loader(YamlFileLoader::class, $container)->load('parameters.yml');
    }

    protected function loader(string $loader, ContainerBuilder $container): FileLoader
    {
        return new $loader($container, $this->locator());
    }

    protected function locator(): FileLocator
    {
        return new FileLocator($this->configPath());
    }

    protected function configPath(): string
    {
        return realpath(__DIR__ . '/../Resources/config');
    }
}
