<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Infrastructure;

use DateTimeImmutable;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as TokenModel;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\TestingLibrary\UnitTestCase;

class TokenTest extends UnitTestCase
{
    private const TEST_TOKEN_ID = '_my_test_token';

    private const TEST_USER_ID = '_testuser';

    /** @var Token */
    private $tokenInfrastructure;

    public function setUp(): void
    {
        parent::setUp();
        $containerFactory = new TestContainerFactory();
        $container        = $containerFactory->create();
        $container->compile();
        $this->tokenInfrastructure = $container->get(TokenInfrastructure::class);
    }

    public function tearDown(): void
    {
        $this->cleanUpTable('oegraphqltoken');

        parent::tearDown();
    }

    public function testRegisterToken(): void
    {
        $this->tokenInfrastructure->registerToken($this->getTokenMock(), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));

        $tokenModel = oxNew(TokenModel::class);
        $tokenModel->load(self::TEST_TOKEN_ID);

        $this->assertTrue($tokenModel->isLoaded());
    }

    public function testIsTokenRegistered(): void
    {
        $this->tokenInfrastructure->registerToken($this->getTokenMock(), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));

        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered(self::TEST_TOKEN_ID));
    }

    public function testIsTokenRegisteredNo(): void
    {
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('not_registered_token'));
    }

    public function testRemoveExpiredTokens(): void
    {
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_first'), new DateTimeImmutable('now'), new DateTimeImmutable('-8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_second'), new DateTimeImmutable('now'), new DateTimeImmutable('-8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_third'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_other', '_otheruser'), new DateTimeImmutable('now'), new DateTimeImmutable('-8 hours'));

        $userModel = oxNew(User::class);
        $userModel->setId(self::TEST_USER_ID);
        $user = new UserDataType($userModel);

        $this->tokenInfrastructure->removeExpiredTokens($user);

        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_first'));
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_second'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_third'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_other'));
    }

    public function testCanIssueToken(): void
    {
        $userModel = oxNew(User::class);
        $userModel->setId(self::TEST_USER_ID);
        $user = new UserDataType($userModel);

        $this->tokenInfrastructure->registerToken($this->getTokenMock('_first'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_second'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_third'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));

        $this->assertTrue($this->tokenInfrastructure->canIssueToken($user, 5));
        $this->assertTrue($this->tokenInfrastructure->canIssueToken($user, 4));  //three are stored, quota is 4, we can issue another token
        $this->assertFalse($this->tokenInfrastructure->canIssueToken($user, 3)); //three are stored, quota is 3, we cannot issue another token right now
        $this->assertFalse($this->tokenInfrastructure->canIssueToken($user, 2));
    }

    public function testTokenDelete(): void
    {
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_first'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_second'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_third'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_other', '_otheruser'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));
        $this->tokenInfrastructure->registerToken($this->getTokenMock('_else', '_elseuser'), new DateTimeImmutable('now'), new DateTimeImmutable('+8 hours'));

        $userModel = oxNew(User::class);
        $userModel->setId(self::TEST_USER_ID);
        $user = new UserDataType($userModel);

        $this->tokenInfrastructure->tokenDelete($user, '_first');
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_first'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_second'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_third'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_other'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_else'));

        $this->tokenInfrastructure->tokenDelete($user);
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_second'));
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_third'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_other'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_else'));

        $this->tokenInfrastructure->tokenDelete(null, '_other');
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_other'));
        $this->assertTrue($this->tokenInfrastructure->isTokenRegistered('_else'));

        $this->tokenInfrastructure->tokenDelete();
        $this->assertFalse($this->tokenInfrastructure->isTokenRegistered('_else'));
    }

    private function getTokenMock(string $tokenId = self::TEST_TOKEN_ID, string $userId = self::TEST_USER_ID): UnencryptedToken
    {
        $claims = new DataSet(
            [
                TokenService::CLAIM_TOKENID        => $tokenId,
                TokenService::CLAIM_SHOPID         => 1,
                TokenService::CLAIM_USERID         => $userId,
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
