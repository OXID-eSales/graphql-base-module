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
use OxidEsales\GraphQL\Base\Framework\NullToken;
use OxidEsales\GraphQL\Base\Framework\UserData;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;
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
        $this->keyRegistry = $this->getMockBuilder(KeyRegistry::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $this->keyRegistry->method('getSignatureKey')
            ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');
        $this->legacy         = $this->getMockBuilder(Legacy::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->authentication = new Authentication(
            $this->keyRegistry,
            $this->legacy,
            new NullToken()
        );
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $shop = [
            'url' => 'https://whatever.com',
            'id'  => 1,
        ];
        $user = [
            'username'   => 'admin',
            'password'   => 'admin',
            'id'         => 'some_nice_user_id',
            'usergroups' => ['onegroup', 'oxidadmin', 'othergroup'],
        ];

        $userData = new UserData($user['id'], $user['usergroups']);
        $this->legacy->method('login')->willReturn($userData);
        $this->legacy->method('getShopUrl')->willReturn($shop['url']);
        $this->legacy->method('getShopId')->willReturn($shop['id']);

        $loginController = new Login($this->authentication);

        $token = (new Parser())->parse($loginController->token($user['username'], $user['password']));

        $data = new ValidationData();
        $data->setIssuer($shop['url']);
        $data->setAudience($shop['url']);

        $this->assertTrue($token->validate($data));
        $this->assertEquals($user['username'], $token->getClaim('username'));
        $this->assertEquals($shop['id'], $token->getClaim('shopid'));
        $this->assertEquals($user['usergroups'], $token->getClaim('groups'));
        $this->assertEquals($user['id'], $token->getClaim('userid'));
    }
}
