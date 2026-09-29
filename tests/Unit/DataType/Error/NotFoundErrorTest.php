<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Error\NotFoundError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundError::class)]
class NotFoundErrorTest extends TestCase
{
    #[Test]
    #[DataProvider('validCodesProvider')]
    public function notFoundError(string $code, string $expectedMessage): void
    {
        $sut = new NotFoundError($code, $identifier = uniqid());

        $this->assertInstanceOf(ErrorInterface::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.not_found.token', NotFoundError::TOKEN);
        $this->assertSame('oegqlb.not_found.user', NotFoundError::USER);
    }

    public static function validCodesProvider(): array
    {
        return [
            [NotFoundError::TOKEN, 'The token was not found.'],
            [NotFoundError::USER, 'The user was not found.'],
        ];
    }

    #[Test]
    public function unknownCodeThrowsException(): void
    {
        $notExistingCode = uniqid();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The error-code "' . $notExistingCode . '" is unknown.');

        new NotFoundError($notExistingCode, uniqid());
    }
}
