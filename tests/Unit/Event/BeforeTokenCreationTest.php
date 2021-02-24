<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Event;

use Lcobucci\JWT\Builder;
use OxidEsales\GraphQL\Base\Event\BeforeTokenCreation;
use OxidEsales\GraphQL\Base\Framework\UserData;
use PHPUnit\Framework\TestCase;

class BeforeTokenCreationTest extends TestCase
{
    public function testBasicGetters(): void
    {
        $userId = 'user-id';
        $groups = ['group' => 'group'];

        $event = new BeforeTokenCreation(
            new Builder(),
            new UserData($userId, $groups)
        );

        $this->assertInstanceOf(
            Builder::class,
            $event->getBuilder()
        );
        $this->assertInstanceOf(
            UserData::class,
            $event->getUserData()
        );
        $this->assertSame(
            $userId,
            $event->getUserData()->getUserId()
        );
        $this->assertSame(
            $groups,
            $event->getUserData()->getUserGroupIds()
        );
    }
}
