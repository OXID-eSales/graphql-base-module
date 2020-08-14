<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Full\Quallified\Namespace\Tests\Integration\Context\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

final class SampleTest extends TokenTestCase
{
    public function testGetSingleActiveActionWithoutProducts(): void
    {
        $result = $this->query('
            query {
                token (username: "admin", password: "admin")
            }
        ');

        $this->assertSame(
            200,
            $result['status']
        );

        // assert that $result['body']['data']['token'] is a valid JWT
    }
}
