<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Event;

use OxidEsales\GraphQL\Base\Event\BeforeAuthorization;
use PHPUnit\Framework\TestCase;
use Lcobucci\JWT\Token;

class BeforeAuthorizationTest extends TestCase
{
    public function testBasicGetters(): void
    {
        $tokenStub = $this->createPartialMock(Token::class, []);

        $event = new BeforeAuthorization(
            $tokenStub,
            'right'
        );

        $this->assertInstanceOf(
            Token::class,
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
