<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Event;

use DateTimeImmutable;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProviderInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Infrastructure\Model\Token as TokenModel;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;

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

    public function testExpireTokenAfterUserPasswordChange(): void
    {
        $userModel = oxNew(User::class);
        $userModel->load('e7af1c3b786fd02906ccd75698f4e6b9');

        $issued = new DateTimeImmutable('now');
        $expires = new DateTimeImmutable('+8 hours');
        $tokenModel = oxNew(TokenModel::class);
        $tokenModel->setId('_changePwdUserToken');
        $tokenModel->assign(
            [
                'OXID' => '_changePwdUserToken',
                'OXSHOPID' => '1',
                'OXUSERID' => 'e7af1c3b786fd02906ccd75698f4e6b9',
                'ISSUED_AT' => $issued->format('Y-m-d H:i:s'),
                'EXPIRES_AT' => $expires->format('Y-m-d H:i:s'),
                'USERAGENT' => '',
                'TOKEN' => 'very_large_string',
            ]
        );
        $tokenModel->save();
        $tokenModel->load('_changePwdUserToken');

        $expiresAtAfterChange = new DateTimeImmutable($tokenModel->getRawFieldData('expires_at'));
        $user = new UserDataType($userModel);

        $this->assertTrue($this->tokenInfrastructure->userHasToken($user, '_changePwdUserToken'));
        $this->assertFalse($expiresAtAfterChange <= new DateTimeImmutable('now'));

        $userModel->setPassword('_newPassword');
        $userModel->save();

        $result = $this->connection->executeQuery(
            "select expires_at from `oegraphqltoken` where oxid=:tokenId",
            ['tokenId' => '_changePwdUserToken']
        );
        $tokenDateAfterChange = $result->fetchOne();

        $this->assertTrue(new DateTimeImmutable($tokenDateAfterChange) <= new DateTimeImmutable('now'));
    }
}
