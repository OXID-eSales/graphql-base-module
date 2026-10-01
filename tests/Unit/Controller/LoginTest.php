<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\Controller\Login;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;
use OxidEsales\GraphQL\Base\ErrorResolver\LoginResolverInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class LoginTest extends TestCase
{
    #[Test]
    public function tokenReturnsPayloadWithTokenValue(): void
    {
        $username = uniqid();
        $password = uniqid();
        $tokenValue = uniqid();

        $exceptionConverterMock = $this->createMock(LoginResolverInterface::class);
        $exceptionConverterMock->method('createToken')
            ->with($username, $password)
            ->willReturn($this->createConfiguredStub(UnencryptedToken::class, ['toString' => $tokenValue]));

        $sut = $this->getSut(loginResolver: $exceptionConverterMock);
        $payload = $sut->token($username, $password);

        $this->assertSame($tokenValue, $payload->token());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function tokenReturnsPayloadWithError(): void
    {
        $username = uniqid();
        $password = uniqid();
        $errorStub = $this->createStub(ErrorInterface::class);

        $exceptionConverterMock = $this->createMock(LoginResolverInterface::class);
        $exceptionConverterMock->method('createToken')
            ->with($username, $password)
            ->willReturn($errorStub);

        $sut = $this->getSut(loginResolver: $exceptionConverterMock);
        $payload = $sut->token($username, $password);

        $this->assertNull($payload->token());
        $this->assertSame($errorStub, $payload->userErrors()[0]);
    }

    #[Test]
    public function loginReturnsPayloadWithLoginData(): void
    {
        $username = uniqid();
        $password = uniqid();
        $loginStub = $this->createStub(LoginInterface::class);

        $exceptionConverterMock = $this->createMock(LoginResolverInterface::class);
        $exceptionConverterMock->method('login')
            ->with($username, $password)
            ->willReturn($loginStub);

        $sut = $this->getSut(loginResolver: $exceptionConverterMock);
        $payload = $sut->login($username, $password);

        $this->assertSame($loginStub, $payload->login());
        $this->assertEmpty($payload->userErrors());
    }

    #[Test]
    public function loginReturnsPayloadWithError(): void
    {
        $username = uniqid();
        $password = uniqid();
        $errorStub = $this->createStub(ErrorInterface::class);

        $exceptionConverterMock = $this->createMock(LoginResolverInterface::class);
        $exceptionConverterMock->method('login')
            ->with($username, $password)
            ->willReturn($errorStub);

        $sut = $this->getSut(loginResolver: $exceptionConverterMock);
        $payload = $sut->login($username, $password);

        $this->assertNull($payload->login());
        $this->assertSame($errorStub, $payload->userErrors()[0]);
    }

    private function getSut(LoginResolverInterface $loginResolver): Login
    {
        return new Login(
            $loginResolver ?? $this->createStub(LoginResolverInterface::class)
        );
    }
}
