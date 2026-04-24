<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Event;

use Lcobucci\JWT\Builder;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Event\BeforeTokenCreation;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(BeforeTokenCreation::class)]
class BeforeTokenCreationTest extends BaseTestCase
{
    #[Test]
    public function getBuilder(): void
    {
        $sut = $this->getSut();

        $this->assertInstanceOf(Builder::class, $sut->getBuilder());
    }

    #[Test]
    public function getUser(): void
    {
        $userId = uniqid();
        $sut = $this->getSut(userId: $userId);

        $this->assertInstanceOf(UserInterface::class, $sut->getUser());
        $this->assertSame($userId, $sut->getUser()->id()->val());
    }

    #[Test]
    public function withClaim(): void
    {
        $claimName = uniqid();
        $claimValue = uniqid();
        $updatedBuilderStub = $this->createStub(Builder::class);

        $builderMock = $this->createMock(Builder::class);
        $builderMock->expects($this->once())
            ->method('withClaim')
            ->with($claimName, $claimValue)
            ->willReturn($updatedBuilderStub);

        $sut = $this->getSut(builder: $builderMock);
        $result = $sut->withClaim($claimName, $claimValue);

        $this->assertSame($updatedBuilderStub, $sut->getBuilder());
        $this->assertSame($sut, $result);
    }

    #[Test]
    public function withHeader(): void
    {
        $headerName = uniqid();
        $headerValue = uniqid();
        $updatedBuilderStub = $this->createStub(Builder::class);

        $builderMock = $this->createMock(Builder::class);
        $builderMock->expects($this->once())
            ->method('withHeader')
            ->with($headerName, $headerValue)
            ->willReturn($updatedBuilderStub);

        $sut = $this->getSut(builder: $builderMock);
        $result = $sut->withHeader($headerName, $headerValue);

        $this->assertSame($updatedBuilderStub, $sut->getBuilder());
        $this->assertSame($sut, $result);
    }

    private function getSut(
        ?Builder $builder = null,
        ?UserInterface $user = null,
        ?string $userId = null
    ): BeforeTokenCreation {
        return new BeforeTokenCreation(
            builder: $builder ?? $this->createStub(Builder::class),
            user: $user ?? new User($this->getUserModelStub($userId))
        );
    }
}
