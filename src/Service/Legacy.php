<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Exception;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;

class Legacy
{
    public const GROUP_ADMIN = 'admin';

    public const GROUP_CUSTOMERS = 'customer';

    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    /** @var ContextInterface */
    private $context;

    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory, ContextInterface $context)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->context             = $context;
    }

    /**
     * @throws InvalidLogin
     */
    public function checkCredentials(string $username, string $password): void
    {
        try {
            /** @var User */
            $user = oxNew(User::class);
            $user->login($username, $password, false);
        } catch (Exception $e) {
            throw new InvalidLogin('Username/password combination is invalid');
        }
    }

    /**
     * @throws InvalidLogin
     */
    public function getUserGroup(string $username): string
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        /** @var \Doctrine\DBAL\Driver\Statement<array> */
        $result       = $queryBuilder->select('OXRIGHTS')
            ->from('oxuser')
            ->where($queryBuilder->expr()->eq('OXUSERNAME', ':username'))
            ->setParameter('username', $username)
            ->execute();

        foreach ($result->fetchAll() as $row) {
            return $this->mapUserGroup($row['OXRIGHTS']);
        }
        # In fact this should not happen because the credentials should already have been checked
        throw new InvalidLogin('User does not exist.');
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

    public function createUniqueIdentifier(): string
    {
        /** @var \OxidEsales\EshopCommunity\Core\UtilsObject */
        $utils = Registry::getUtilsObject();

        return $utils->generateUId();
    }

    /**
     * @throws InvalidLogin
     */
    private function mapUserGroup(?string $dbGroup): string
    {
        if ($dbGroup === 'user') {
            return self::GROUP_CUSTOMERS;
        }

        if ($dbGroup == 'malladmin') {
            return self::GROUP_ADMIN;
        }

        if ((int) $dbGroup == $this->context->getCurrentShopId()) {
            return self::GROUP_ADMIN;
        }

        throw new InvalidLogin('Invalid usergroup');
    }
}
