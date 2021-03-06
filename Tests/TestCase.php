<?php

namespace Helldar\EnvSync\Frameworks\Symfony\Tests;

use Helldar\EnvSync\Frameworks\Symfony\Tests\Concerns\Bundle;
use Helldar\EnvSync\Frameworks\Symfony\Tests\Concerns\Files;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

abstract class TestCase extends BaseTestCase
{
    use Files;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /** @var \Symfony\Bundle\FrameworkBundle\Console\Application */
    protected $application;

    protected function setUp(): void
    {
        $this->mockContainer();
        $this->mockApplication();
    }

    protected function mockContainer(): void
    {
        $this->container = $this->getMockBuilder(ContainerInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\Symfony\Component\HttpKernel\Kernel
     */
    protected function mockKernel()
    {
        $kernel = $this->getMockBuilder(Kernel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $kernel->expects($this->once())
            ->method('getBundles')
            ->will($this->returnValue([
                new Bundle(),
            ]));

        $kernel->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($this->container));

        return $kernel;
    }

    protected function mockApplication(): void
    {
        $this->application = new Application($this->mockKernel());
    }

    protected function tester(Command $command): CommandTester
    {
        return new CommandTester($command);
    }

    protected function call(string $name, array $options = []): string
    {
        $command = $this->application->find($name);

        $tester = $this->tester($command);

        $tester->execute(array_merge(['command' => $name], $options));

        return $tester->getDisplay();
    }
}
