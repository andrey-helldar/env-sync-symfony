<?php

namespace Helldar\EnvSync\Frameworks\Symfony\Tests\Unit;

use Helldar\EnvSync\Frameworks\Symfony\Tests\TestCase;
use Helldar\Support\Exceptions\DirectoryNotFoundException;

final class ConfigurableTest extends TestCase
{
    protected $fixture_expected = 'expected-config';

    public function testCustomPath()
    {
        $this->call('env:sync', ['--path' => $this->path]);

        $this->assertFileExists($this->targetPath());
        $this->assertFileEquals($this->expected(), $this->targetPath());
    }

    public function testCustomPathFailed()
    {
        $this->expectException(DirectoryNotFoundException::class);

        $this->call('env:sync', ['--path' => '/foo']);
    }
}
