<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(AuthenticationError::class)]
class AuthenticationErrorTest extends AbstractErrorTestCase
{
    #[Test]
    public function authenticationError(): void
    {
        $sut = new AuthenticationError($code = uniqid(), $message = uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.authentication.token_quota_exceeded', AuthenticationError::TOKEN_QUOTA_EXCEEDED);
    }

    #[Test]
    #[DataProvider('validCodesProvider')]
    public function fromCode(string $code, string $expectedMessage): void
    {
        $sut = AuthenticationError::fromCode($code);

        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->message());
    }

    public static function validCodesProvider(): array
    {
        return [
            [AuthenticationError::TOKEN_QUOTA_EXCEEDED, 'The token quota for this user has been exceeded.'],
        ];
    }
}
