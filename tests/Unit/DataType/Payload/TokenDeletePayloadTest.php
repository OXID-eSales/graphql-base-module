<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenDeletePayload;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenDeletePayload::class)]
class TokenDeletePayloadTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $count = random_int(1, 100);
        $userErrors = [$this->createStub(ErrorInterface::class)];

        $sut = new TokenDeletePayload($count, $userErrors);

        $this->assertSame($count, $sut->deletedCount());
        $this->assertSame($userErrors, $sut->userErrors());
    }

    #[Test]
    public function nullDeletedCount(): void
    {
        $sut = new TokenDeletePayload(null);

        $this->assertNull($sut->deletedCount());
        $this->assertEmpty($sut->userErrors());
    }
}
