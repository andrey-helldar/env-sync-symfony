<?php

namespace Helldar\EnvSync\Frameworks\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

class Extension extends BaseExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->xmlLoader($container)->load('command.xml');
        $this->yamlLoader($container)->load('env-sync.yml');
    }

    protected function xmlLoader(ContainerBuilder $container): XmlFileLoader
    {
        return new XmlFileLoader($container, $this->locator());
    }

    protected function yamlLoader(ContainerBuilder $container): YamlFileLoader
    {
        return new YamlFileLoader($container, $this->locator());
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
