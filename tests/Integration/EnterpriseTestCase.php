<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration;

abstract class EnterpriseTestCase extends TokenTestCase
{
    protected function setUp(): void
    {
        if ($this->getConfig()->getEdition() !== 'EE') {
            $this->markTestSkipped('Skip EE related tests for CE/PE edition');

            return;
        }

        parent::setUp();
    }
}
