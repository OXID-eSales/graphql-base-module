<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Error;

use OxidEsales\GraphQL\Base\DataType\Error\IdAlreadyExistsError;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(IdAlreadyExistsError::class)]
final class ConflictErrorTest extends AbstractErrorTestCase
{
    #[Test]
    public function conflictError(): void
    {
        $sut = new IdAlreadyExistsError($code = uniqid(), $message = uniqid(), $identifier = uniqid());

        $this->assertInstanceOf(ErrorInterface::class, $sut);
        $this->assertSame($code, $sut->code());
        $this->assertSame($message, $sut->message());
        $this->assertSame($identifier, $sut->identifier());
    }
}
