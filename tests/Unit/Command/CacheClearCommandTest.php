<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Command;

use OxidEsales\GraphQL\Base\Command\CacheClearCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(CacheClearCommand::class)]
class CacheClearCommandTest extends TestCase
{
    #[Test]
    public function executeClearsCacheAndReportsSuccess(): void
    {
        $cacheMock = $this->createMock(CacheInterface::class);
        $cacheMock->expects($this->once())->method('clear')->willReturn(true);

        $tester = new CommandTester($this->getSut(cache: $cacheMock));

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Schema cache cleared.', $tester->getDisplay());
    }

    #[Test]
    public function executeReportsFailureWhenCacheClearFails(): void
    {
        $cacheStub = $this->createStub(CacheInterface::class);
        $cacheStub->method('clear')->willReturn(false);

        $tester = new CommandTester($this->getSut(cache: $cacheStub));

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Failed to clear schema cache.', $tester->getDisplay());
    }

    private function getSut(?CacheInterface $cache = null): CacheClearCommand
    {
        return new CacheClearCommand(
            cache: $cache ?? $this->createStub(CacheInterface::class)
        );
    }
}
