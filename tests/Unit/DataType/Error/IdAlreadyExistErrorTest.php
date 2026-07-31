<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractError;
use OxidEsales\GraphQL\Base\DataType\Error\IdAlreadyExistError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(IdAlreadyExistError::class)]
final class IdAlreadyExistErrorTest extends AbstractErrorTestCase
{
    #[Test]
    public function idAlreadyExistError(): void
    {
        $sut = new IdAlreadyExistError($code = uniqid(), $message = uniqid(), $identifier = uniqid());

        $this->assertInstanceOf(AbstractError::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }
}
