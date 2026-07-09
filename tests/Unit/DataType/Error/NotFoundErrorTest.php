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
    public function notFoundError(): void
    {
        $sut = new NotFoundError($code = uniqid(), $message = uniqid(), $identifier = uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.not_found.token', NotFoundError::TOKEN);
        $this->assertSame('oegqlb.not_found.user', NotFoundError::USER);
    }

    #[Test]
    public function fromCodeWithoutOptionalArguments(): void
    {
        $sut = NotFoundError::fromCode(NotFoundError::USER);
        $this->assertSame('', $sut->identifier());
    }

    #[Test]
    public function fromCodeReturnsCorrectIdentifier(): void
    {
        $identifier = uniqid();
        $sut = NotFoundError::fromCode(NotFoundError::TOKEN, $identifier);

        $this->assertSame($identifier, $sut->identifier());
    }

    public static function validCodesProvider(): array
    {
        return [
            [NotFoundError::TOKEN, 'The token was not found.'],
            [NotFoundError::USER, 'The user was not found.'],
        ];
    }

    protected function getConcreteError(): string
    {
        return NotFoundError::class;
    }
}
