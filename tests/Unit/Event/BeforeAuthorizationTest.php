<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Event;

use OxidEsales\GraphQL\Base\Event\BeforeAuthorization;
use OxidEsales\GraphQL\Base\Framework\NullToken;
use PHPUnit\Framework\TestCase;

class BeforeAuthorizationTest extends TestCase
{
    public function testBasicGetters(): void
    {
        $event = new BeforeAuthorization(
            new NullToken(),
            'right'
        );

        $this->assertInstanceOf(
            NullToken::class,
            $event->getToken()
        );
        $this->assertSame(
            'right',
            $event->getRight()
        );
        $this->assertNull(
            $event->getAuthorized()
        );
    }
}
