<?php

namespace Helldar\EnvSync\Frameworks\Symfony\Tests\Unit;

use Helldar\EnvSync\Frameworks\Symfony\Tests\TestCase;
use Helldar\Support\Exceptions\DirectoryNotFoundException;

final class MainTest extends TestCase
{
    public function testCustomPath()
    {
        $result = $this->call('env:sync', ['--path' => $this->path]);

        $this->assertStringContainsString('Searching...', $result);
        $this->assertStringContainsString('The found keys were successfully saved to the .env.example file.', $result);

        $this->assertFileExists($this->targetPath());
        $this->assertFileEquals($this->expected(), $this->targetPath());
    }

    public function testCustomPathFailed()
    {
        $this->expectException(DirectoryNotFoundException::class);

        $this->call('env:sync', ['--path' => '/foo']);
    }
}
