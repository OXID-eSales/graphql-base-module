<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use OxidEsales\GraphQL\Base\DataType\Error\AuthorizationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(AuthorizationError::class)]
class AuthorizationErrorTest extends AbstractErrorTestCase
{
    #[Test]
    public function authorizationError(): void
    {
        $sut = new AuthorizationError($code = uniqid(), $message = uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
    }

    #[Test]
    public function errorCodes(): void
    {
        $this->assertSame('oegqlb.authorized.delete_token', AuthorizationError::UNAUTHORIZED_DELETE_TOKEN);
        $this->assertSame('oegqlb.authorized.view_token', AuthorizationError::UNAUTHORIZED_VIEW_TOKEN);
    }

    #[Test]
    #[DataProvider('validCodesProvider')]
    public function fromCode(string $code, string $expectedMessage): void
    {
        $sut = AuthorizationError::fromCode($code);

        $this->assertSame($code, $sut->code());
        $this->assertSame($expectedMessage, $sut->message());
    }

    public static function validCodesProvider(): array
    {
        return [
            [AuthorizationError::UNAUTHORIZED_DELETE_TOKEN, 'You are not authorized to delete this token.'],
            [AuthorizationError::UNAUTHORIZED_VIEW_TOKEN, 'You are not authorized to view this token.'],
        ];
    }
}
