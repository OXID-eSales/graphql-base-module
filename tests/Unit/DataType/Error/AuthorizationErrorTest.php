<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Error\AuthorizationError;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuthorizationError::class)]
class AuthorizationErrorTest extends TestCase
{
    #[Test]
    #[DataProvider('validCodesProvider')]
    public function authorizationError(string $code, string $expectedMessage): void
    {
        $sut = new AuthorizationError($code);

        $this->assertInstanceOf(ErrorInterface::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->getMessage());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.authorized.delete_token', AuthorizationError::UNAUTHORIZED_DELETE_TOKEN);
        $this->assertSame('oegqlb.authorized.view_token', AuthorizationError::UNAUTHORIZED_VIEW_TOKEN);
    }

    public static function validCodesProvider(): array
    {
        return [
            [AuthorizationError::UNAUTHORIZED_DELETE_TOKEN, 'You are not authorized to delete this token.'],
            [AuthorizationError::UNAUTHORIZED_VIEW_TOKEN, 'You are not authorized to view this token.'],
        ];
    }

    #[Test]
    public function unknownCodeThrowsException(): void
    {
        $notExistingCode = uniqid();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The error-code "' . $notExistingCode . '" is unknown.');

        new AuthorizationError($notExistingCode);
    }
}
