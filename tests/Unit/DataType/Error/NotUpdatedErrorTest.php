<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use OxidEsales\GraphQL\Base\DataType\Error\NotUpdatedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotUpdatedError::class)]
final class NotUpdatedErrorTest extends TestCase
{
    #[Test]
    public function notUpdatedError(): void
    {
        $sut = new NotUpdatedError($code = uniqid(), $message = uniqid(), $identifier = uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }
}
