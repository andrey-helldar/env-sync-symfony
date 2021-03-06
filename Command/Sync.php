<?php

namespace Helldar\EnvSync\Frameworks\Symfony\Command;

use Composer\Config;
use Helldar\EnvSync\Services\Syncer;
use Helldar\Support\Facades\Helpers\Filesystem\File;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Sync extends Command
{
    /** @var \Symfony\Component\Console\Input\InputInterface */
    protected $input;

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /** @var \Composer\Config */
    protected $config;

    /** @var \Helldar\EnvSync\Services\Syncer */
    protected $syncer;

    public function __construct(Config $config, Syncer $syncer)
    {
        parent::__construct();

        $this->config = $config;
        $this->syncer = $syncer;

        $this->setSyncerConfig();
    }

    protected function configure()
    {
        $this
            ->setName('env:sync')
            ->setDescription('Synchronizing environment settings with a preset')
            ->addOption('path', null, InputArgument::OPTIONAL, 'Gets the path to scan for files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;

        $this->info('Searching...');

        $filename = $this->filename();

        $this->sync($filename);

        $this->info("The found keys were successfully saved to the {$filename} file.");

        return 0;
    }

    protected function sync(string $filename): void
    {
        $this->syncer
            ->path($this->path())
            ->filename($filename)
            ->store();
    }

    protected function path(): string
    {
        return $this->optionPath() ?: $this->realPath();
    }

    protected function filename(): string
    {
        return '.env.example';
    }

    protected function optionPath(): ?string
    {
        return $this->input->getOption('path');
    }

    protected function realPath(): string
    {
        $vendor = $this->config->get('vendor-dir');

        return realpath($vendor . '/../');
    }

    protected function info(string $message): void
    {
        $this->line($message, 'info');
    }

    protected function line(string $string, string $style = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled);
    }

    protected function syncerConfig(): array
    {
        $path = realpath(__DIR__ . '/../Resources/config');

        $filename = 'parameters.yml';

        if (! File::exists($path . DIRECTORY_SEPARATOR . $filename)) {
            return [];
        }

        $container = $this->container();

        $loader = $this->loader($container, $path);

        $loader->load($filename);

        return $container->getParameterBag()->all();
    }

    protected function setSyncerConfig(): void
    {
        if ($config = $this->syncerConfig()) {
            $this->syncer->setConfig($config);
        }
    }

    protected function container(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    protected function loader(ContainerBuilder $container, string $path): YamlFileLoader
    {
        return new YamlFileLoader($container, new FileLocator($path));
    }
}
