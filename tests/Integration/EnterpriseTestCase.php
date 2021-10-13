<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration;

use OxidEsales\Facts\Facts;

abstract class EnterpriseTestCase extends TokenTestCase
{
    protected function setUp(): void
    {
        $facts = new Facts();

        if ($facts->getEdition() !== 'EE') {
            $this->markTestSkipped('Skip EE related tests for CE/PE edition');

            return;
        }

        parent::setUp();
    }
}
