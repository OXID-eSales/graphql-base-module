<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Event;

use DateTimeImmutable;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;

class UserDeleteTest extends IntegrationTestCase
{
    private const TEST_TOKEN_ID = '_my_test_token';

    private const TEST_USER_ID = '_testuser';

    /** @var TokenInfrastructure */
    private $tokenInfrastructure;

    public function setUp(): void
    {
        parent::setUp();
        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create();
        $container->compile();
        $this->tokenInfrastructure = $container->get(TokenInfrastructure::class);
    }

    public function testInvalidateTokenAfterDeleteUser(): void
    {
        $userModel = oxNew(User::class);
        $userModel->setId('_testUser');
        $userModel->setPassword('_testPassword');
        $userModel->assign(['oxusername' => '_testUsername']);
        $userModel->save();

        $this->tokenInfrastructure->registerToken(
            $this->getTokenMock('_deletedUser'),
            new DateTimeImmutable('now'),
            new DateTimeImmutable('+8 hours')
        );

        $user = new UserDataType($userModel);
        $this->assertTrue($this->tokenInfrastructure->userHasToken($user, '_deletedUser'));

        $userModel->delete(self::TEST_USER_ID);
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_deletedUser'));
    }

    private function getTokenMock(
        string $tokenId = self::TEST_TOKEN_ID,
        string $userId = self::TEST_USER_ID
    ): UnencryptedToken {
        $claims = new DataSet(
            [
                TokenService::CLAIM_TOKENID => $tokenId,
                TokenService::CLAIM_SHOPID => 1,
                TokenService::CLAIM_USERID => $userId,
            ],
            ''
        );

        $token = $this->getMockBuilder(UnencryptedToken::class)
            ->getMock();
        $token->method('claims')->willReturn($claims);
        $token->method('toString')->willReturn('here_is_the_string_token');

        return $token;
    }
}
