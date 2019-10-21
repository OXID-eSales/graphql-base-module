<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Service;

use OxidEsales\GraphQL\Service\AuthorizationService;
use PHPUnit\Framework\TestCase;

class AuthorizationServiceTest extends TestCase
{
    public function testIsAllowedWithoutPermissions()
    {
        $auth = new AuthorizationService([]);
        $this->assertFalse($auth->isAllowed(''));
    }

}
