<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class InvalidSignatureTest extends BaseTestCase
{
    public function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testBrokenToken(): void
    {
        $legacy = $this->createPartialMock(LegacyService::class, ['login', 'getShopId', 'getShopUrl']);
        $legacy->method('login')->willReturn($this->getUserDataStub($this->getUserModelStub('the_admin_oxid')));
        $token = $this->getTokenService($legacy, null)->createToken('admin', 'admin');

        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . substr($token->toString(), 0, -10);

        $requestReader = new RequestReader(
            $this->getTokenValidator($legacy),
            $this->getJwtConfigurationBuilder($legacy)
        );

        $this->expectException(InvalidToken::class);
        $requestReader->getAuthToken();
    }
}
