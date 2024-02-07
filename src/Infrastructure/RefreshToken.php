<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use DateTimeImmutable;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\DataType\RefreshToken as RefreshTokenDataType;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Model\RefreshToken as RefreshTokenModel;
use PDO;

class RefreshToken
{
    private ?RefreshTokenDataType $token = null;

    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private Legacy $legacyInfrastructure
    ) {
    }

    public function registerToken(
        string $token,
        DateTimeImmutable $time,
        DateTimeImmutable $expire,
        UserDataType $user
    ): RefreshTokenDataType {
        $model = new RefreshTokenModel();
        $model->assign(
            [
                'OXID' => Legacy::createUniqueIdentifier(),
                'OXSHOPID' => $this->legacyInfrastructure->getShopId(),
                'OXUSERID' => $user->id()->val(),
                'ISSUED_AT' => $time->format('Y-m-d H:i:s'),
                'EXPIRES_AT' => $expire->format('Y-m-d H:i:s'),
                'USERAGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'TOKEN' => $token,
            ]
        );
        $model->save();

        $token = new RefreshTokenDataType($model);
        $this->token = $token;

        return $token;
    }

    public function isTokenRegistered(string $tokenId): bool
    {
        $model = oxNew(Model\RefreshToken::class);
        $model->load($tokenId);

        return $model->isLoaded();
    }

    public function removeExpiredTokens(UserDataType $user): void
    {
        $queryBuilder = $this->queryBuilderFactory->create()
            ->delete('oegraphqlrefreshtoken')
            ->where('OXUSERID = :userId')
            ->andWhere('EXPIRES_AT <= NOW()')
            ->setParameters([
                'userId' => (string)$user->id(),
            ]);

        $queryBuilder->execute();
    }

    public function canIssueToken(UserDataType $user, int $quota): bool
    {
        $return = false;

        $result = $this->queryBuilderFactory->create()
            ->select('count(oegraphqlrefreshtoken.oxid) as counted')
            ->from('oegraphqlrefreshtoken')
            ->where('OXUSERID = :userId')
            ->setParameters([
                'userId' => (string)$user->id(),
            ])
            ->execute();

        if (is_object($result)) {
            $return = (int)$result->fetch(PDO::FETCH_ASSOC)['counted'] < $quota;
        }

        return $return;
    }

    public function tokenDelete(?UserDataType $user = null, ?string $tokenId = null, ?int $shopId = null): int
    {
        $parameters = [];
        $condition = 'where';

        $queryBuilder = $this->queryBuilderFactory->create()
            ->delete('oegraphqlrefreshtoken');

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

        $result = $queryBuilder->execute();

        return is_object($result) ? $result->columnCount() : (int)$result;
    }

    public function getTokenUser(string $token): UserDataType
    {
        if ($this->token) {
            $userId = $this->token->customerId()->val();
        } else {
            $queryBuilder = $this->queryBuilderFactory->create()
                ->select('OXUSERID')
                ->from('oegraphqlrefreshtoken')
                ->where('TOKEN = :token')
                ->andWhere('EXPIRES_AT > NOW()')
                ->setParameter('token', $token);
            $userId = $queryBuilder->execute()->fetchOne();

            if ($userId === false) {
                throw new InvalidToken('Invalid refresh token');
            }
        }

        $userModel = $this->legacyInfrastructure->getUserModel($userId);

        return new UserDataType($userModel);
    }

    public function userHasToken(UserDataType $user, string $tokenId): bool
    {
        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder
            ->select('count(OXID)')
            ->from('oegraphqlrefreshtoken')
            ->where('OXID = :tokenId')
            ->andWhere('OXUSERID = :userId')
            ->setParameters([
                'tokenId' => $tokenId,
                'userId' => (string)$user->id(),
            ]);

        $result = $queryBuilder->execute();

        if (is_object($result)) {
            return $result->fetch(PDO::FETCH_COLUMN) > 0;
        }

        return false;
    }
}
