<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use PHPUnit\Framework\TestCase;

final class InvalidTokenTest extends TestCase
{
    public function testExceptionCategory(): void
    {
        $invalidTokenException = InvalidToken::invalidToken();

        $this->assertSame(ErrorCategories::PERMISSIONERRORS, $invalidTokenException->getCategory());
    }

    public function testIsClientSafe(): void
    {
        $invalidTokenException = InvalidToken::invalidToken();

        $this->assertTrue($invalidTokenException->isClientSafe());
    }

    public function testInvalidToken(): void
    {
        $invalidTokenException = InvalidToken::invalidToken();

        $this->assertSame('The token is invalid', $invalidTokenException->getMessage());
    }

    public function testUnableToParse(): void
    {
        $invalidTokenException = InvalidToken::unableToParse();

        $this->assertSame('Unable to parse token', $invalidTokenException->getMessage());
    }

    public function testUserBlocked(): void
    {
        $invalidTokenException = InvalidToken::userBlocked();

        $this->assertSame('User is blocked', $invalidTokenException->getMessage());
    }
}
