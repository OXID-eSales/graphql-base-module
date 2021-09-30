<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Controller;

use OxidEsales\GraphQL\Base\Controller\Login;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoginTest extends BaseTestCase
{
    /** @var Authentication */
    private $authentication;

    /** @var KeyRegistry|MockObject */
    private $keyRegistry;

    /** @var Legacy|MockObject */
    private $legacy;

    /** @var JwtConfigurationBuilder */
    private $jwtConfigurationBuilder;

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
            new TokenService(),
            new EventDispatcher()
        );

        $this->jwtConfigurationBuilder = new JwtConfigurationBuilder(
            $this->keyRegistry,
            $this->legacy
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
        ];

        $userData = new User($this->getUserModelStub($user['id']));

        $this->legacy->method('login')->willReturn($userData);
        $this->legacy->method('getShopUrl')->willReturn($shop['url']);
        $this->legacy->method('getShopId')->willReturn($shop['id']);

        $loginController = new Login($this->authentication);

        $jwt        = $loginController->token($user['username'], $user['password']);
        $config     = $this->jwtConfigurationBuilder->getConfiguration();
        $token      = $config->parser()->parse($jwt);
        $validator  = $config->validator();

        $this->assertTrue($validator->validate($token, ...$config->validationConstraints()));
        $this->assertEquals($user['username'], $token->claims()->get(Authentication::CLAIM_USERNAME));
        $this->assertEquals($shop['id'], $token->claims()->get(Authentication::CLAIM_SHOPID));
        $this->assertEquals($user['id'], $token->claims()->get(Authentication::CLAIM_USERID));
    }

    public function testCreateTokenWithMissingPassword(): void
    {
        $shop = [
            'url' => 'https://whatever.com',
            'id'  => 1,
        ];

        $this->legacy->method('getShopUrl')->willReturn($shop['url']);
        $this->legacy->method('getShopId')->willReturn($shop['id']);
        $this->legacy->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $loginController = new Login($this->authentication);

        $jwt       = $loginController->token('none');
        $config     = $this->jwtConfigurationBuilder->getConfiguration();
        $token     = $config->parser()->parse($jwt);
        $validator = $config->validator();

        $this->assertTrue($validator->validate($token, ...$config->validationConstraints()));
        $this->assertEquals($shop['id'], $token->claims()->get(Authentication::CLAIM_SHOPID));
        $this->assertNotEmpty($token->claims()->get(Authentication::CLAIM_USERID));
    }

    public function testCreateTokenWithMissingUsername(): void
    {
        $shop = [
            'url' => 'https://whatever.com',
            'id'  => 1,
        ];

        $this->legacy->method('getShopUrl')->willReturn($shop['url']);
        $this->legacy->method('getShopId')->willReturn($shop['id']);
        $this->legacy->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $loginController = new Login($this->authentication);

        $jwt       = $loginController->token(null, 'none');
        $config     = $this->jwtConfigurationBuilder->getConfiguration();
        $token     = $config->parser()->parse($jwt);
        $validator = $config->validator();

        $this->assertTrue($validator->validate($token, ...$config->validationConstraints()));
        $this->assertEquals($shop['id'], $token->claims()->get(Authentication::CLAIM_SHOPID));
        $this->assertNotEmpty($token->claims()->get(Authentication::CLAIM_USERID));
    }

    public function testCreateAnonymousToken(): void
    {
        $shop = [
            'url' => 'https://whatever.com',
            'id'  => 1,
        ];

        $this->legacy->method('getShopUrl')->willReturn($shop['url']);
        $this->legacy->method('getShopId')->willReturn($shop['id']);
        $this->legacy->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $loginController = new Login($this->authentication);

        $jwt       = $loginController->token();
        $config     = $this->jwtConfigurationBuilder->getConfiguration();
        $token     = $config->parser()->parse($jwt);
        $validator = $config->validator();

        $this->assertTrue($validator->validate($token, ...$config->validationConstraints()));
        $this->assertEquals($shop['id'], $token->claims()->get(Authentication::CLAIM_SHOPID));
        $this->assertNotEmpty($token->claims()->get(Authentication::CLAIM_USERID));
    }
}
