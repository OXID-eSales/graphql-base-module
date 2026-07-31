<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\BooleanPayload;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BooleanPayload::class)]
class BooleanPayloadTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $success = (bool)rand(0, 1);
        $userErrors = [$this->createStub(ErrorInterface::class)];

        $sut = new BooleanPayload($success, $userErrors);

        $this->assertSame($success, $sut->success());
        $this->assertSame($userErrors, $sut->userErrors());
    }

    #[Test]
    public function nullSuccess(): void
    {
        $sut = new BooleanPayload(null);

        $this->assertNull($sut->success());
        $this->assertEmpty($sut->userErrors());
    }
}
