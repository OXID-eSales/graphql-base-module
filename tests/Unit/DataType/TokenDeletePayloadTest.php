<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use OxidEsales\GraphQL\Base\DataType\TokenDeletePayload;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TokenDeletePayload::class)]
class TokenDeletePayloadTest extends AbstractPayloadTestCase
{
    #[Test]
    public function fields(): void
    {
        $count = random_int(1, 100);
        $sut = new TokenDeletePayload($count);

        $this->assertSame($count, $sut->deletedCount());
    }

    #[Test]
    public function nullDeletedCount(): void
    {
        $sut = new TokenDeletePayload(null);

        $this->assertNull($sut->deletedCount());
    }

    protected function createPayload(array $userErrors = []): PayloadInterface
    {
        return new TokenDeletePayload(null, $userErrors);
    }
}
