<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use OxidEsales\GraphQL\Base\DataType\Error\AuthenticationError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(AuthenticationError::class)]
class AuthenticationErrorTest extends AbstractErrorTestCase
{
    #[Test]
    public function extendsAbstractError(): void
    {
        $sut = new AuthenticationError(uniqid(), uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
    }

    public static function validCodesProvider(): array
    {
        return [
            [AuthenticationError::TOKEN_QUOTA_EXCEEDED, 'The token quota for this user has been exceeded.'],
        ];
    }

    protected function getConcreteError(): string
    {
        return AuthenticationError::class;
    }
}
