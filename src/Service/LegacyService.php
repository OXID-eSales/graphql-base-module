<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidLoginException;

class LegacyService implements LegacyServiceInterface
{
    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    /** @var ContextInterface */
    private $context;

    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory, ContextInterface $context)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->context = $context;
    }

    /**
     * @throws InvalidLoginException
     */
    public function checkCredentials(string $username, string $password): void
    {
        try {
            oxNew(User::class)->login($username, $password, false);
        } catch (\Exception $e) {
            throw new InvalidLoginException('Username/password combination is invalid');
        }
    }

    /**
     * @throws InvalidLoginException
     */
    public function getUserGroup(string $username): string
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $result = $queryBuilder->select('OXRIGHTS')
            ->from('oxuser')
            ->where($queryBuilder->expr()->eq('OXUSERNAME', ':username'))
            ->setParameter('username', $username)
            ->execute();
        foreach ($result->fetchAll() as $row) {
            return $this->mapUserGroup($row['OXRIGHTS']);
        }
        # In fact this should not happen because the credentials should already have been checked
        throw new InvalidLoginException('User does not exist.');
    }

    /**
     * @return mixed
     */
    public function getConfigParam(string $param)
    {
        return Registry::getConfig()->getConfigParam($param);
    }

    public function getShopUrl(): string
    {
        return Registry::getConfig()->getShopUrl();
    }

    public function getShopId(): int
    {
        return $this->context->getCurrentShopId();
    }

    public function getLanguageId(): int
    {
        $requestParameter = $_GET['lang'];
        if ($requestParameter === null) {
            return (int) Registry::getLang()->getBaseLanguage();
        }
        return (int) $requestParameter;
    }

    /**
     * @throws InvalidLoginException
     */
    private function mapUserGroup(?string $dbGroup): string
    {
        if ($dbGroup === 'user') {
            return LegacyServiceInterface::GROUP_CUSTOMERS;
        }
        if ($dbGroup == 'malladmin') {
            return LegacyServiceInterface::GROUP_ADMIN;
        }
        if (intval($dbGroup) == $this->context->getCurrentShopId()) {
            return LegacyServiceInterface::GROUP_ADMIN;
        }
        throw new InvalidLoginException('Invalid usergroup');
    }

    public function createUniqueIdentifier(): string
    {
        /** @var \OxidEsales\EshopCommunity\Core\UtilsObject */
        $utils = Registry::getUtilsObject();
        return $utils->generateUId();
    }
}
