<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use OxidEsales\GraphQL\Base\Framework\AnonymousUserData;
use PHPUnit\Framework\TestCase;

class AnonymousUserDataTest extends TestCase
{
    public function testGetUserId(): void
    {
        $userData = new AnonymousUserData();
        $this->assertNotEmpty($userData->getUserId());
    }

    public function testGetUserGroupIds(): void
    {
        $userData = new AnonymousUserData();
        $this->assertSame(['oxidanonymous'], $userData->getUserGroupIds());
    }
}
