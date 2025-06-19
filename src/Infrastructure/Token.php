<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Lcobucci\JWT\UnencryptedToken;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\ArrayParameterType;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use PDO;

class Token
{
    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory
    ) {
    }

    public function registerToken(UnencryptedToken $token, DateTimeImmutable $time, DateTimeImmutable $expire): void
    {
        $storage = oxNew(Model\Token::class);
        $storage->assign(
            [
                'OXID' => $token->claims()->get(TokenService::CLAIM_TOKENID),
                'OXSHOPID' => $token->claims()->get(TokenService::CLAIM_SHOPID),
                'OXUSERID' => $token->claims()->get(TokenService::CLAIM_USERID),
                'ISSUED_AT' => $time->format('Y-m-d H:i:s'),
                'EXPIRES_AT' => $expire->format('Y-m-d H:i:s'),
                'USERAGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'TOKEN' => $token->toString(),
            ]
        );
        $storage->save();
    }

    public function isTokenRegistered(string $tokenId): bool
    {
        $storage = oxNew(Model\Token::class);
        $storage->load($tokenId);

        return $storage->isLoaded();
    }

    public function isTokenExpired(string $tokenId): bool
    {
        $queryBuilder = $this->queryBuilderFactory->create()
            ->select('oxid')
            ->from('oegraphqltoken')
            ->where('OXID = :tokenId')
            ->andWhere('EXPIRES_AT <= NOW()')
            ->setParameters([
                'tokenId' => $tokenId,
            ]);

        $result = $queryBuilder->executeQuery();

        return $result->fetchOne() > 0;
    }

    public function removeExpiredTokens(UserInterface $user): void
    {
        $queryBuilder = $this->queryBuilderFactory->create()
            ->delete('oegraphqltoken')
            ->where('OXUSERID = :userId')
            ->andWhere('EXPIRES_AT <= NOW()')
            ->setParameters([
                'userId' => (string)$user->id(),
            ]);

        $queryBuilder->executeQuery();
    }

    public function deleteOrphanedTokens(string $userId): void
    {
        $builder = $this->queryBuilderFactory->create()
            ->delete('oegraphqltoken')
            ->where("oxuserid = :userid")
            ->setParameters(['userid' => $userId]);

        $builder->executeStatement();
    }

    public function canIssueToken(UserInterface $user, int $quota): bool
    {
        $result = $this->queryBuilderFactory->create()
            ->select('count(oegraphqltoken.oxid) as counted')
            ->from('oegraphqltoken')
            ->where('OXUSERID = :userId')
            ->setParameters([
                'userId' => (string)$user->id(),
            ])
            ->fetchOne();

        return (int)$result < $quota;
    }

    public function tokenDelete(?UserInterface $user = null, ?string $tokenId = null, ?int $shopId = null): int
    {
        $parameters = [];
        $condition = 'where';

        $queryBuilder = $this->queryBuilderFactory->create()
            ->delete('oegraphqltoken');

        if ($tokenId) {
            $queryBuilder->$condition('OXID = :tokenId');
            $parameters['tokenId'] = $tokenId;
            $condition = 'andWhere';
        }

        if ($user) {
            $queryBuilder->$condition('OXUSERID = :userId');
            $parameters['userId'] = (string)$user->id();
        }

        if ($shopId) {
            $queryBuilder->$condition('OXSHOPID = :shopId');
            $parameters['shopId'] = $shopId;
        }

        $queryBuilder->setParameters($parameters);

        return (int) $queryBuilder->executeStatement();
    }

    public function userHasToken(UserInterface $user, string $tokenId): bool
    {
        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder
            ->select('count(OXID)')
            ->from('oegraphqltoken')
            ->where('OXID = :tokenId')
            ->andWhere('OXUSERID = :userId')
            ->setParameters([
                'tokenId' => $tokenId,
                'userId' => (string)$user->id(),
            ]);

        $result = $queryBuilder->executeQuery();

        return $result->fetchOne() > 0;
    }

    public function invalidateUserTokens(string $userId): void
    {
        $queryBuilder = $this->queryBuilderFactory->create()
            ->update('oegraphqltoken')
            ->where('OXUSERID = :userId')
            ->set('EXPIRES_AT', 'NOW()')
            ->setParameters([
                'userId' => $userId,
            ]);

        $queryBuilder->executeStatement();
    }
}
