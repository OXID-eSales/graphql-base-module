<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

use OxidEsales\GraphQL\Base\Controller\Login;
use OxidEsales\GraphQL\Base\Service\AuthenticationService;
use OxidEsales\GraphQL\Base\Service\KeyRegistryInterface;
use OxidEsales\GraphQL\Base\Service\LegacyServiceInterface;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    /** @var AuthenticationService */
    private $authenticationService;

    public function setUp(): void
    {
        $this->keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $this->keyRegistry->method('getSignatureKey')
            ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');
        $this->legacyService         = $this->getMockBuilder(LegacyServiceInterface::class)->getMock();
        $this->authenticationService = new AuthenticationService($this->keyRegistry, $this->legacyService);
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $this->legacyService->method('checkCredentials');
        $this->legacyService->method('getUserGroup')->willReturn(LegacyServiceInterface::GROUP_ADMIN);
        $this->legacyService->method('getShopUrl')->willReturn('https:/whatever.com');
        $this->legacyService->method('getShopId')->willReturn(1);
        $loginController = new Login($this->authenticationService);

        $token = $loginController->token('admin', 'admin');

        $this->assertIsString($token);
    }
}
