<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use OxidEsales\GraphQL\Base\DataType\Error\ValidationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ValidationError::class)]
class ValidationErrorTest extends AbstractErrorTestCase
{
    #[Test]
    public function validationError(): void
    {
        $sut = new ValidationError($code = uniqid(), $message = uniqid(), $value = uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
        $this->assertSame($value, $sut->value());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.validation.credentials', ValidationError::CREDENTIALS);
        $this->assertSame('oegqlb.validation.fingerprint', ValidationError::FINGERPRINT);
        $this->assertSame('oegqlb.validation.refresh_token', ValidationError::REFRESH_TOKEN);
    }

    #[Test]
    public function fromCodeReturnsCorrectValue(): void
    {
        $value = uniqid();
        $sut = ValidationError::fromCode(ValidationError::CREDENTIALS, $value);

        $this->assertSame($value, $sut->value());
    }

    public static function validCodesProvider(): array
    {
        return [
            [ValidationError::CREDENTIALS, 'The provided credentials are invalid.'],
            [ValidationError::FINGERPRINT, 'The fingerprint validation failed.'],
            [ValidationError::REFRESH_TOKEN, 'The provided refresh token is invalid.'],
        ];
    }

    protected function getConcreteError(): string
    {
        return ValidationError::class;
    }
}
