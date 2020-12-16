<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

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
    /** @var Authentication */
    private $authentication;

    /** @var KeyRegistry|MockObject */
    private $keyRegistry;

    /** @var Legacy|MockObject */
    private $legacy;

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

        $jwt       = $loginController->token($user['username'], $user['password']);
        $config    = $this->authentication->getConfig();
        $token     = $config->parser()->parse($jwt);
        $validator = $config->validator();

        $this->assertTrue($validator->validate($token, ...$config->validationConstraints()));
        $this->assertEquals($user['username'], $token->claims()->get(Authentication::CLAIM_USERNAME));
        $this->assertEquals($shop['id'], $token->claims()->get(Authentication::CLAIM_SHOPID));
        $this->assertEquals($user['usergroups'], $token->claims()->get(Authentication::CLAIM_GROUPS));
        $this->assertEquals($user['id'], $token->claims()->get(Authentication::CLAIM_USERID));
    }
}
