<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;
use OxidEsales\GraphQL\Base\Service\LoginExceptionConverter;
use OxidEsales\GraphQL\Base\Service\LoginServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class LoginExceptionConverterTest extends TestCase
{
    #[Test]
    public function createTokenReturnsTokenOnSuccess(): void
    {
        $username = uniqid();
        $password = uniqid();
        $tokenStub = $this->createStub(UnencryptedToken::class);

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->method('createToken')
            ->with($username, $password)
            ->willReturn($tokenStub);

        $sut = $this->getSut(tokenService: $tokenServiceMock);

        $this->assertSame($tokenStub, $sut->createToken($username, $password));
    }

    #[Test]
    public function createTokenReturnsTokenOnSuccessWithNull(): void
    {
        $username = null;
        $password = null;
        $tokenStub = $this->createStub(UnencryptedToken::class);

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->method('createToken')
            ->with($username, $password)
            ->willReturn($tokenStub);

        $sut = $this->getSut(tokenService: $tokenServiceMock);

        $this->assertSame($tokenStub, $sut->createToken($username, $password));
    }

    #[Test]
    public function createTokenReturnsValidationErrorOnInvalidLogin(): void
    {
        $username = uniqid();
        $password = uniqid();

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->method('createToken')
            ->with($username, $password)
            ->willThrowException(new InvalidLogin(uniqid()));

        $sut = $this->getSut(tokenService: $tokenServiceMock);
        $result = $sut->createToken($username, $password);

        $this->assertEquals(ValidationError::fromCode(ValidationError::CREDENTIALS), $result);
    }

    #[Test]
    public function createTokenReturnsAuthenticationErrorOnTokenQuota(): void
    {
        $username = uniqid();
        $password = uniqid();

        $tokenServiceMock = $this->createMock(TokenService::class);
        $tokenServiceMock->method('createToken')
            ->with($username, $password)
            ->willThrowException(new TokenQuota(uniqid()));

        $sut = $this->getSut(tokenService: $tokenServiceMock);
        $result = $sut->createToken($username, $password);

        $this->assertEquals(AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED), $result);
    }

    #[Test]
    public function loginReturnsLoginInterfaceOnSuccess(): void
    {
        $username = uniqid();
        $password = uniqid();
        $loginStub = $this->createStub(LoginInterface::class);

        $loginServiceMock = $this->createMock(LoginServiceInterface::class);
        $loginServiceMock->method('login')
            ->with($username, $password)
            ->willReturn($loginStub);

        $sut = $this->getSut(loginService: $loginServiceMock);

        $this->assertSame($loginStub, $sut->login($username, $password));
    }

    #[Test]
    public function loginReturnsLoginInterfaceOnSuccessWithNull(): void
    {
        $username = null;
        $password = null;
        $loginStub = $this->createStub(LoginInterface::class);

        $loginServiceMock = $this->createMock(LoginServiceInterface::class);
        $loginServiceMock->method('login')
            ->with($username, $password)
            ->willReturn($loginStub);

        $sut = $this->getSut(loginService: $loginServiceMock);

        $this->assertSame($loginStub, $sut->login($username, $password));
    }

    #[Test]
    public function loginReturnsValidationErrorOnInvalidLogin(): void
    {
        $username = uniqid();
        $password = uniqid();

        $loginServiceMock = $this->createMock(LoginServiceInterface::class);
        $loginServiceMock->method('login')
            ->with($username, $password)
            ->willThrowException(new InvalidLogin(uniqid()));

        $sut = $this->getSut(loginService: $loginServiceMock);
        $result = $sut->login($username, $password);

        $this->assertEquals(ValidationError::fromCode(ValidationError::CREDENTIALS), $result);
    }

    #[Test]
    public function loginReturnsAuthenticationErrorOnTokenQuota(): void
    {
        $username = uniqid();
        $password = uniqid();

        $loginServiceMock = $this->createMock(LoginServiceInterface::class);
        $loginServiceMock->method('login')
            ->with($username, $password)
            ->willThrowException(new TokenQuota(uniqid()));

        $sut = $this->getSut(loginService: $loginServiceMock);
        $result = $sut->login($username, $password);

        $this->assertEquals(AuthenticationError::fromCode(AuthenticationError::TOKEN_QUOTA_EXCEEDED), $result);
    }

    private function getSut(
        ?TokenService $tokenService = null,
        ?LoginServiceInterface $loginService = null,
    ): LoginExceptionConverter {
        return new LoginExceptionConverter(
            $tokenService ?? $this->createStub(TokenService::class),
            $loginService ?? $this->createStub(LoginServiceInterface::class),
        );
    }
}
