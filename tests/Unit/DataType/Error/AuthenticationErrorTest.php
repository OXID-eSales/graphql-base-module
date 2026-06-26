<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuthenticationError::class)]
class AuthenticationErrorTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $code = uniqid();
        $message = uniqid();
        $sut = new AuthenticationError($code, $message);

        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
    }

    #[Test]
    #[DataProvider('validCodesProvider')]
    public function fromCodeReturnsCorrectCodeAndMessage(string $code, string $expectedMessage): void
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
