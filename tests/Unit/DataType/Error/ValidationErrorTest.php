<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValidationError::class)]
class ValidationErrorTest extends TestCase
{
    #[Test]
    #[DataProvider('validCodesProvider')]
    public function validationError(string $code, string $expectedMessage): void
    {
        $sut = new ValidationError($code, $value = uniqid());

        $this->assertInstanceOf(ErrorInterface::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->getMessage());
        $this->assertSame($value, $sut->value());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.validation.fingerprint', ValidationError::FINGERPRINT);
        $this->assertSame('oegqlb.validation.refresh_token', ValidationError::REFRESH_TOKEN);
    }

    public static function validCodesProvider(): array
    {
        return [
            [ValidationError::FINGERPRINT, 'The fingerprint validation failed.'],
            [ValidationError::REFRESH_TOKEN, 'The provided refresh token is invalid.'],
        ];
    }

    #[Test]
    public function unknownCodeThrowsException(): void
    {
        $notExistingCode = uniqid();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The error-code "' . $notExistingCode . '" is unknown.');

        new ValidationError($notExistingCode, uniqid());
    }
}
