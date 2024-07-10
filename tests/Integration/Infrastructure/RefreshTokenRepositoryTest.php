<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Infrastructure;

use DateTime;
use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProviderInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepository;
use OxidEsales\GraphQL\Base\Infrastructure\RefreshTokenRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RefreshTokenRepository::class)]
class RefreshTokenRepositoryTest extends IntegrationTestCase
{
    public function testGetNewRefreshTokenGivesCorrectlyFilledDataType(): void
    {
        $sut = $this->getSut();

        $userId = uniqid();
        $token = $sut->getNewRefreshToken(
            userId: $userId,
            lifeTime: $lifetime = '+1 month',
        );
        $id = $token->id()->val();

        $this->assertNotEmpty($id);
        $this->assertEquals(1, $token->shopId()->val());
        $this->assertSame($userId, $token->customerId()->val());
        $this->assertTrue(strlen($token->token()) === 255);

        $this->assertSame(
            (new DateTime('now'))->format("Y-m-d H:i"),
            $token->createdAt()->format("Y-m-d H:i")
        );

        $this->assertSame(
            (new DateTime($lifetime))->format("Y-m-d H:i"),
            $token->expiresAt()->format("Y-m-d H:i")
        );
    }

    public function testGetNewRefreshTokenRegistersTokenInDatabase(): void
    {
        $sut = $this->getSut();

        $token = $sut->getNewRefreshToken(
            userId: uniqid(),
            lifeTime: '+1 month',
        );
        $id = $token->id()->val();

        $this->assertTrue($this->checkRefreshTokenWithIdExists($id));
    }

    public function testRemoveExpiredTokens(): void
    {
        $this->addToken(
            oxid: $expiredId = uniqid(),
            expires: (new DateTime('-1 day'))->format(DateTime::ATOM)
        );

        $this->addToken(
            oxid: $notExpiredId = uniqid(),
            expires: (new DateTime('+1 day'))->format(DateTime::ATOM)
        );

        $sut = $this->getSut();
        $sut->removeExpiredTokens();

        $this->assertFalse($this->checkRefreshTokenWithIdExists($expiredId));
        $this->assertTrue($this->checkRefreshTokenWithIdExists($notExpiredId));
    }

    private function getDbConnection(): Connection
    {
        return $this->get(ConnectionProviderInterface::class)->get();
    }

    public function getSut(): RefreshTokenRepositoryInterface
    {
        return $this->get(RefreshTokenRepositoryInterface::class);
    }

    private function checkRefreshTokenWithIdExists(string $oxid): bool
    {
        $result = $this->getDbConnection()->executeQuery(
            "select count(*) from `oegraphqlrefreshtoken` where OXID=:oxid",
            ['oxid' => $oxid]
        );

        return $result->fetchOne() > 0;
    }

    public function addToken(
        string $oxid,
        string $expires,
        string $userId = null,
    ): void {
        $insertTokensQuery = "insert into `oegraphqlrefreshtoken` (OXID, OXUSERID, EXPIRES_AT)
            values (:oxid, :oxuserid, :expires)";
        $this->getDbConnection()->executeQuery($insertTokensQuery, [
            "oxid" => $oxid,
            "oxuserid" => $userId ?? uniqid(),
            "expires" => $expires,
        ]);
    }
}
