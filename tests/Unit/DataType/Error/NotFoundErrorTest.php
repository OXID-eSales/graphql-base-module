<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\NotFoundError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundError::class)]
class NotFoundErrorTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $code = uniqid();
        $identifier = uniqid();
        $message = uniqid();
        $sut = new NotFoundError($code, $message, $identifier);

        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }

    #[Test]
    #[DataProvider('validCodesProvider')]
    public function fromCodeReturnsCorrectCodeAndMessage(string $code, string $expectedMessage): void
    {
        $identifier = uniqid();
        $sut = NotFoundError::fromCode($code, $identifier);

        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }

    public static function validCodesProvider(): array
    {
        return [
            [NotFoundError::NOT_FOUND, 'The requested resource was not found.'],
            [NotFoundError::NOT_FOUND_TOKEN, 'The requested token was not found.'],
            [NotFoundError::NOT_FOUND_USER, 'The requested user was not found.'],
        ];
    }
}
