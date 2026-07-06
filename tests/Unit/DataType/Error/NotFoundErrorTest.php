<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use OxidEsales\GraphQL\Base\DataType\Error\NotFoundError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(NotFoundError::class)]
class NotFoundErrorTest extends AbstractErrorTestCase
{
    #[Test]
    public function extendsAbstractError(): void
    {
        $sut = new NotFoundError(uniqid(), uniqid(), uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
    }

    #[Test]
    public function identifierField(): void
    {
        $identifier = uniqid();
        $sut = new NotFoundError(uniqid(), uniqid(), $identifier);

        $this->assertSame($identifier, $sut->identifier());
    }

    #[Test]
    #[DataProvider('validCodesProvider')]
    public function fromCodeReturnsCorrectCodeAndMessageAndIdentifier(string $code, string $expectedMessage): void
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

    protected function getConcreteError(): string
    {
        return NotFoundError::class;
    }
}
