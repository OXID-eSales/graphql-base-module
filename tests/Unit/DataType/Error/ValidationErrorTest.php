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
    public function extendsAbstractError(): void
    {
        $sut = new ValidationError(uniqid(), uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
    }

    public static function validCodesProvider(): array
    {
        return [
            [ValidationError::INVALID_CREDENTIALS, 'The provided credentials are invalid.'],
            [ValidationError::INVALID_FINGERPRINT, 'The fingerprint validation failed.'],
            [ValidationError::INVALID_REFRESH_TOKEN, 'The provided refresh token is invalid.'],
        ];
    }

    protected function getConcreteError(): string
    {
        return ValidationError::class;
    }
}
