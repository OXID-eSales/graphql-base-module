<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\CreationPayload;
use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(CreationPayload::class)]
class CreationPayloadTest extends AbstractPayloadTestCase
{
    #[Test]
    public function fields(): void
    {
        $sut = new CreationPayload(true);

        $this->assertTrue($sut->success());
    }

    #[Test]
    public function nullSuccess(): void
    {
        $sut = new CreationPayload(null);

        $this->assertNull($sut->success());
    }

    protected function createPayload(array $userErrors = []): PayloadInterface
    {
        return new CreationPayload(null, $userErrors);
    }
}
