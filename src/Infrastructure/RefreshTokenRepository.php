<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use DateTimeImmutable;
use Doctrine\DBAL\Result;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\DataType\RefreshToken as RefreshTokenDataType;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Model\RefreshTokenModelFactoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    private ?RefreshTokenDataType $token = null;

    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private Legacy $legacyInfrastructure,
        private RefreshTokenModelFactoryInterface $refreshTokenModelFactory,
    ) {
    }

    public function getNewRefreshToken(string $userId, string $lifeTime): RefreshTokenDataType
    {
        $model = $this->refreshTokenModelFactory->create();

        $model->assign([
            'OXID' => $this->legacyInfrastructure->createUniqueIdentifier(),
            'OXSHOPID' => $this->legacyInfrastructure->getShopId(),
            'OXUSERID' => $userId,
            'ISSUED_AT' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            'EXPIRES_AT' => (new DateTimeImmutable($lifeTime))->format('Y-m-d H:i:s'),
            'TOKEN' => substr(bin2hex(random_bytes(128)), 0, 255),
        ]);
        $model->save();

        return new RefreshTokenDataType($model);
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
            $return = (int)$result->fetchOne() < $quota;
        }

        return $return;
    }

    private function getTokenUserId(string $refreshToken): string
    {
        $queryBuilder = $this->queryBuilderFactory->create()
            ->select('OXUSERID')
            ->from('oegraphqlrefreshtoken')
            ->where('TOKEN = :token')
            ->andWhere('EXPIRES_AT > NOW()')
            ->setParameter('token', $refreshToken);
        $result = $queryBuilder->execute();

        if ($result instanceof Result === false) {
            throw new InvalidToken('Invalid refresh token');
        }

        return (string)$result->fetchOne();
    }

    public function getTokenUser(string $refreshToken): UserDataType
    {
        $userId = null;

        if ($this->token) {
            $userId = (string)$this->token->customerId();
        }

        if (!$userId) {
            $userId = $this->getTokenUserId($refreshToken);
        }

        $userModel = $this->legacyInfrastructure->getUserModel($userId);

        $isAnonymous = !$userModel->getId();

        if ($isAnonymous) {
            $userModel->setId($this->legacyInfrastructure::createUniqueIdentifier());
        }

        return new UserDataType($userModel, $isAnonymous);
    }
}
