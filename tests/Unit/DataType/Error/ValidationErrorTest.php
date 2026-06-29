<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidationError::class)]
class ValidationErrorTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $code = uniqid();
        $message = uniqid();
        $sut = new ValidationError($code, $message);

        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
    }

    #[Test]
    #[DataProvider('validCodesProvider')]
    public function fromCodeReturnsCorrectCodeAndMessage(string $code, string $expectedMessage): void
    {
        $sut = ValidationError::fromCode($code);

        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->message());
    }

    public static function validCodesProvider(): array
    {
        return [
            [ValidationError::INVALID_CREDENTIALS, 'The provided credentials are invalid.'],
            [ValidationError::INVALID_FINGERPRINT, 'The fingerprint validation failed.'],
            [ValidationError::INVALID_REFRESH_TOKEN, 'The provided refresh token is invalid.'],
        ];
    }
}
