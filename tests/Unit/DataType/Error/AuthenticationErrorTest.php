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
    #[DataProvider('validCodesProvider')]
    public function authenticationError(string $code, string $expectedMessage): void
    {
        $sut = new AuthenticationError($code);

        $this->assertInstanceOf(AbstractError::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->message());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.authentication.credentials_incorrect', AuthenticationError::CREDENTIALS_INCORRECT);
        $this->assertSame('oegqlb.authentication.token_quota_exceeded', AuthenticationError::TOKEN_QUOTA_EXCEEDED);
    }

    public static function validCodesProvider(): array
    {
        return [
            [AuthenticationError::CREDENTIALS_INCORRECT, 'The provided credentials are invalid.'],
            [AuthenticationError::TOKEN_QUOTA_EXCEEDED, 'The token quota for this user has been exceeded.'],
        ];
    }
}
