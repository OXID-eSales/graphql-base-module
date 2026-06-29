<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\Error\PayloadInterface;
use OxidEsales\GraphQL\Base\DataType\Token;
use OxidEsales\GraphQL\Base\DataType\TokensPayload;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as TokenModel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TokensPayload::class)]
class TokensPayloadTest extends AbstractPayloadTestCase
{
    #[Test]
    public function fields(): void
    {
        $token = new Token($this->createStub(TokenModel::class));
        $sut = new TokensPayload([$token]);

        $this->assertSame([$token], $sut->tokens());
    }

    #[Test]
    public function nullTokens(): void
    {
        $sut = new TokensPayload(null);

        $this->assertNull($sut->tokens());
    }

    protected function createPayload(array $userErrors = []): PayloadInterface
    {
        return new TokensPayload(null, $userErrors);
    }
}
