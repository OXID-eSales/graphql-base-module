<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\Error\AbstractPayload;
use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractPayload::class)]
abstract class AbstractPayloadTestCase extends TestCase
{
    abstract protected function createPayload(array $userErrors = []): PayloadInterface;

    #[Test]
    public function userErrors(): void
    {
        $errorStub = $this->createStub(ErrorInterface::class);
        $sut = $this->createPayload([$errorStub]);

        $this->assertSame([$errorStub], $sut->userErrors());
    }

    #[Test]
    public function emptyUserErrors(): void
    {
        $sut = $this->createPayload();

        $this->assertEmpty($sut->userErrors());
    }
}
