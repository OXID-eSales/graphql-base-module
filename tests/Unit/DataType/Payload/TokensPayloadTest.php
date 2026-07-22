<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace DataType\Payload;

use OxidEsales\GraphQL\Base\DataType\Error\ErrorInterface;
use OxidEsales\GraphQL\Base\DataType\Payload\TokensPayload;
use OxidEsales\GraphQL\Base\DataType\Token;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as TokenModel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokensPayload::class)]
class TokensPayloadTest extends TestCase
{
    #[Test]
    public function fields(): void
    {
        $token = new Token($this->createStub(TokenModel::class));
        $userErrors = [$this->createStub(ErrorInterface::class)];

        $sut = new TokensPayload([$token], $userErrors);

        $this->assertSame([$token], $sut->tokens());
        $this->assertSame($userErrors, $sut->userErrors());
    }

    #[Test]
    public function nullTokens(): void
    {
        $sut = new TokensPayload(null);

        $this->assertNull($sut->tokens());
        $this->assertEmpty($sut->userErrors());
    }
}
