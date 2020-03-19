<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use OxidEsales\GraphQL\Base\Controller\Login;
use OxidEsales\GraphQL\Base\Service\AuthenticationService;
use OxidEsales\GraphQL\Base\Service\KeyRegistryInterface;
use OxidEsales\GraphQL\Base\Service\LegacyServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    /** @var AuthenticationService */
    private $authenticationService;

    /** @var KeyRegistryInterface|MockObject */
    private $keyRegistry;

    /** @var LegacyServiceInterface|MockObject */
    private $legacyService;

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
        $shop = [
            'url' => 'https://whatever.com',
            'id'  => 1,
        ];
        $user = [
            'username' => 'admin',
            'password' => 'admin',
            'group'    => LegacyServiceInterface::GROUP_ADMIN,
        ];

        $this->legacyService->method('checkCredentials');
        $this->legacyService->method('getUserGroup')->willReturn($user['group']);
        $this->legacyService->method('getShopUrl')->willReturn($shop['url']);
        $this->legacyService->method('getShopId')->willReturn($shop['id']);
        $loginController = new Login($this->authenticationService);

        $token = (new Parser())->parse($loginController->token($user['username'], $user['password']));

        $data = new ValidationData();
        $data->setIssuer($shop['url']);
        $data->setAudience($shop['url']);

        $this->assertTrue($token->validate($data));
        $this->assertEquals($user['username'], $token->getClaim('username'));
        $this->assertEquals($shop['id'], $token->getClaim('shopid'));
        $this->assertEquals($user['group'], $token->getClaim('group'));
    }
}
