<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use OxidEsales\GraphQL\Base\Framework\NullToken;
use PHPUnit\Framework\TestCase;

class NullTokenTest extends TestCase
{
    public function testNotExpired(): void
    {
        $token = new NullToken();
        $this->assertFalse(
            $token->isExpired()
        );
    }
}
