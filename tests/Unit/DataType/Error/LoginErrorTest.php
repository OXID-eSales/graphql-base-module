<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\LoginError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LoginError::class)]
class LoginErrorTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $code = uniqid();
        $message = uniqid();
        $sut = new LoginError($code, $message);

        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
    }

    #[Test]
    #[DataProvider('validCodesProvider')]
    public function fromCodeReturnsCorrectCodeAndMessage(string $code, string $expectedMessage): void
    {
        $sut = LoginError::fromCode($code);

        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->message());
    }

    public static function validCodesProvider(): array
    {
        return [
            [LoginError::INVALID_CREDENTIALS, 'The provided credentials are invalid.'],
            [LoginError::TOKEN_QUOTA_EXCEEDED, 'The token quota for this user has been exceeded.'],
        ];
    }
}
