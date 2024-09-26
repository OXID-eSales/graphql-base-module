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
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProviderInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as TokenModel;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class PasswordChangeTest extends IntegrationTestCase
{
    /** @var TokenInfrastructure */
    private $tokenInfrastructure;

    /** @var ConnectionProviderInterface */
    private $connection;

    public function setUp(): void
    {
        parent::setUp();
        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create();
        $container->compile();
        $this->tokenInfrastructure = $container->get(TokenInfrastructure::class);
        $this->connection = $container->get(ConnectionProviderInterface::class)->get();
    }

    #[RunInSeparateProcess]
    public function testExpireTokenAfterUserPasswordChange(): void
    {
        $userModel = $this->getUserModel();
        $tokenModel = $this->getTokenModel();

        $expiresAtBeforeChange = new DateTimeImmutable($tokenModel->getRawFieldData('expires_at'));
        $user = new UserDataType($userModel);

        $this->assertTrue($this->tokenInfrastructure->userHasToken($user, '_changePwdUserToken'));
        $this->assertFalse($expiresAtBeforeChange <= new DateTimeImmutable('now'));

        $userModel->setPassword('_newPassword');
        $userModel->save();

        $result = $this->connection->executeQuery(
            "select expires_at from `oegraphqltoken` where oxid=:tokenId",
            ['tokenId' => '_changePwdUserToken']
        );
        $expiresAtAfterChange = $result->fetchOne();

        $this->assertTrue(new DateTimeImmutable($expiresAtAfterChange) <= new DateTimeImmutable('now'));
    }

    #[RunInSeparateProcess]
    public function testKeepTokenAfterUserChangeEventAndNoPwdChange(): void
    {
        $userModel = $this->getUserModel();
        $tokenModel = $this->getTokenModel();

        $expiresAtBeforeChange = new DateTimeImmutable($tokenModel->getRawFieldData('expires_at'));
        $user = new UserDataType($userModel);

        $this->assertTrue($this->tokenInfrastructure->userHasToken($user, '_changePwdUserToken'));
        $this->assertFalse($expiresAtBeforeChange <= new DateTimeImmutable('now'));

        $userModel->assign(['oxfname' => 'Test']);
        $userModel->save();

        $result = $this->connection->executeQuery(
            "select expires_at from `oegraphqltoken` where oxid=:tokenId",
            ['tokenId' => '_changePwdUserToken']
        );
        $expiresAtAfterChange = $result->fetchOne();

        $this->assertFalse(new DateTimeImmutable($expiresAtAfterChange) <= new DateTimeImmutable('now'));
    }

    private function getUserModel(): User
    {
        $userModel = oxNew(User::class);
        $userModel->setId('_testUser');
        $userModel->setPassword('_testPassword');
        $userModel->assign(['oxusername' => '_testUsername']);
        $userModel->save();

        return $userModel;
    }

    private function getTokenModel(): TokenModel
    {
        $issued = new DateTimeImmutable('now');
        $expires = new DateTimeImmutable('+8 hours');
        $tokenModel = oxNew(TokenModel::class);
        $tokenModel->setId('_changePwdUserToken');
        $tokenModel->assign(
            [
                'OXID' => '_changePwdUserToken',
                'OXSHOPID' => '1',
                'OXUSERID' => '_testUser',
                'ISSUED_AT' => $issued->format('Y-m-d H:i:s'),
                'EXPIRES_AT' => $expires->format('Y-m-d H:i:s'),
                'USERAGENT' => '',
                'TOKEN' => 'very_large_string',
            ]
        );
        $tokenModel->save();
        $tokenModel->load('_changePwdUserToken');

        return $tokenModel;
    }
}
