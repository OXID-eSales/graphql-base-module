<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\BooleanPayload;
use OxidEsales\GraphQL\Base\Tests\Unit\DataType\AbstractPayloadTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(BooleanPayload::class)]
class BooleanPayloadTest extends AbstractPayloadTestCase
{
    #[Test]
    public function fields(): void
    {
        $sut = new BooleanPayload(true);

        $this->assertTrue($sut->success());
    }

    #[Test]
    public function nullSuccess(): void
    {
        $sut = new BooleanPayload(null);

        $this->assertNull($sut->success());
    }

    protected function createPayload(array $userErrors = []): PayloadInterface
    {
        return new BooleanPayload(null, $userErrors);
    }
}
