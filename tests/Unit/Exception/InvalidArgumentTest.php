<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\InvalidArgument;
use PHPUnit\Framework\TestCase;


final class InvalidArgumentTest extends TestCase
{
    public function testExceptionCategory(): void
    {
        $invalidArgumentException = new InvalidArgument(['VALID', 'EQUALS'], 'INVALID_EQUALS');

        $this->assertSame(ErrorCategories::REQUESTERROR, $invalidArgumentException->getCategory());
        $this->assertSame($invalidArgumentException->getMessage(), '"equals" is only allowed to be one of
        "VALID, EQUALS"  was "INVALID_EQUALS"');
    }
}
