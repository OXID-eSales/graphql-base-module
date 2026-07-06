<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokenPayload;
use OxidEsales\GraphQL\Base\Tests\Unit\DataType\AbstractPayloadTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TokenPayload::class)]
class TokenPayloadTest extends AbstractPayloadTestCase
{
    #[Test]
    public function fields(): void
    {
        $tokenString = uniqid();
        $sut = new TokenPayload($tokenString);

        $this->assertSame($tokenString, $sut->token());
    }

    #[Test]
    public function nullToken(): void
    {
        $sut = new TokenPayload(null);

        $this->assertNull($sut->token());
    }

    protected function createPayload(array $userErrors = []): PayloadInterface
    {
        return new TokenPayload(null, $userErrors);
    }
}
