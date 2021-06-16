<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use OxidEsales\GraphQL\Base\Framework\UserData;
use PHPUnit\Framework\TestCase;

class UserDataTest extends TestCase
{
    public function testGetUserId(): void
    {
        $id       = 'user-id';
        $userData = new UserData($id);
        $this->assertNotEmpty($id, $userData->getUserId());
    }
}
