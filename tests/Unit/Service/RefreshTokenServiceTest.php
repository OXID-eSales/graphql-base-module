<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepositoryInterface;
use OxidEsales\GraphQL\Base\Service\ModuleConfiguration;
use OxidEsales\GraphQL\Base\Service\RefreshTokenService;
use OxidEsales\GraphQL\Base\Service\RefreshTokenServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RefreshTokenService::class)]
class RefreshTokenServiceTest extends TestCase
{
    public function testRefreshTokenMethodGeneratesNewTokenByRefreshToken(): void
    {
        $sut = $this->getSut(
            refreshTokRepo: $repositoryMock = $this->createMock(RefreshTokenRepositoryInterface::class),
            tokenService: $tokenServiceMock = $this->createMock(TokenService::class)
        );

        $refreshToken = uniqid();

        $repositoryMock->method('getTokenUser')->with($refreshToken)->willReturn(
            $userStub = $this->createStub(UserInterface::class)
        );

        $tokenServiceMock->method('createTokenForUser')->with($userStub)->willReturn(
            $this->createConfiguredStub(UnencryptedToken::class, [
                'toString' => $tokenValue = uniqid()
            ])
        );

        $this->assertSame($tokenValue, $sut->refreshToken($refreshToken, uniqid()));
    }

    public function getSut(
        RefreshTokenRepositoryInterface $refreshTokRepo = null,
        ModuleConfiguration $moduleConfiguration = null,
        TokenService $tokenService = null,
    ): RefreshTokenServiceInterface {
        return new RefreshTokenService(
            refreshTokenRepository: $refreshTokRepo ?? $this->createStub(RefreshTokenRepositoryInterface::class),
            moduleConfiguration: $moduleConfiguration ?? $this->createStub(ModuleConfiguration::class),
            tokenService: $tokenService ?? $this->createStub(TokenService::class),
        );
    }
}
