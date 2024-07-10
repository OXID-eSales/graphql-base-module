<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\RefreshTokenInterface;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepositoryInterface;
use OxidEsales\GraphQL\Base\Service\FingerprintServiceInterface;
use OxidEsales\GraphQL\Base\Service\ModuleConfiguration;
use OxidEsales\GraphQL\Base\Service\RefreshTokenService;
use OxidEsales\GraphQL\Base\Service\RefreshTokenServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

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

    public function testRefreshTokenMethodTriggersTokenValidation(): void
    {
        $sut = $this->getSut(
            fingerprintService: $fingerprintServiceSpy = $this->createMock(FingerprintServiceInterface::class),
        );

        $fingerprintHash = uniqid();

        $fingerprintServiceSpy->expects($this->once())
            ->method('validateFingerprintHashToCookie')->with($fingerprintHash);

        $sut->refreshToken(uniqid(), $fingerprintHash);
    }

    public function testCreateRefreshTokenForUserTriggersExpiredTokensRemoval(): void
    {
        $sut = $this->getSut(
            refreshTokRepo: $repositorySpy = $this->createMock(RefreshTokenRepositoryInterface::class),
        );

        $repositorySpy->expects($this->once())->method('removeExpiredTokens');

        $sut->createRefreshTokenForUser($this->createStub(UserInterface::class));
    }

    public function testCreateRefreshReturnsRepositoryCreatedTokenValue(): void
    {
        $sut = $this->getSut(
            refreshTokRepo: $repositoryMock = $this->createMock(RefreshTokenRepositoryInterface::class),
            moduleConfiguration: $this->createConfiguredStub(ModuleConfiguration::class, [
                'getRefreshTokenLifeTime' => $lifetime = uniqid()
            ]),
        );

        $userId = uniqid();
        $userStub = $this->createConfiguredStub(UserInterface::class, ['id' => new ID($userId)]);

        $repositoryMock->method('getNewRefreshToken')->with($userId, $lifetime)->willReturn(
            $this->createConfiguredStub(RefreshTokenInterface::class, [
                'token' => $newToken = uniqid()
            ])
        );

        $this->assertSame($newToken, $sut->createRefreshTokenForUser($userStub));
    }

    public function getSut(
        RefreshTokenRepositoryInterface $refreshTokRepo = null,
        ModuleConfiguration $moduleConfiguration = null,
        TokenService $tokenService = null,
        FingerprintServiceInterface $fingerprintService = null,
    ): RefreshTokenServiceInterface {
        return new RefreshTokenService(
            refreshTokenRepository: $refreshTokRepo ?? $this->createStub(RefreshTokenRepositoryInterface::class),
            moduleConfiguration: $moduleConfiguration ?? $this->createStub(ModuleConfiguration::class),
            tokenService: $tokenService ?? $this->createStub(TokenService::class),
            fingerprintService: $fingerprintService ?? $this->createStub(FingerprintServiceInterface::class),
        );
    }
}
