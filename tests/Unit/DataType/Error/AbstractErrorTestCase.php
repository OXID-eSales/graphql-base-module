<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractError::class)]
abstract class AbstractErrorTestCase extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $code = uniqid();
        $message = uniqid();
        $sut = new class ($code, $message) extends AbstractError {
        };

        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
    }
}
