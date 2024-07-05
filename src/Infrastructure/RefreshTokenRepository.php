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
use OxidEsales\GraphQL\Base\Infrastructure\Model\RefreshToken as RefreshTokenModel;
use PDO;

class RefreshTokenRepository
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
                'ANONYMOUS' => $user->isAnonymous(),
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

    public function getTokenUserId(string $token): string
    {
        $queryBuilder = $this->queryBuilderFactory->create()
        ->select('OXUSERID')
        ->from('oegraphqlrefreshtoken')
        ->where('TOKEN = :token')
        ->andWhere('EXPIRES_AT > NOW()')
        ->setParameter('token', $token);
        $result = $queryBuilder->execute();

        if ($result instanceof Result === false) {
            throw new InvalidToken('Invalid refresh token');
        }

        return (string) $result->fetchOne();
    }

    public function getTokenUser(string $token): UserDataType
    {
        $userId = null;

        if ($this->token) {
            $userId = (string) $this->token->customerId();
        }

        if (!$userId) {
            $userId = $this->getTokenUserId($token);
        }

        $userModel = $this->legacyInfrastructure->getUserModel($userId);

        $isAnonymous = !$userModel->getId();

        if ($isAnonymous) {
            $userModel->setId($this->legacyInfrastructure::createUniqueIdentifier());
        }

        return new UserDataType($userModel, $isAnonymous);
    }
}
