<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\GraphQL\Exception\InvalidLoginException;

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
     * @param string $username
     * @param string $password
     * @throws InvalidLoginException
     */
    public function checkCredentials(string $username, string $password)
    {
        try {
            oxNew(User::class)->login($username, $password, false);
        } catch (\Exception $e) {
            throw new InvalidLoginException('Username/password combination is invalid');
        }
    }

    /**
     * @param string $username
     * @return string
     * @throws InvalidLoginException
     */
    public function getUserGroup(string $username): string {

        $queryBuilder = $this->queryBuilderFactory->create();
        $result =$queryBuilder->select('OXRIGHTS')
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
     * @return string
     */
    public function getShopUrl(): string
    {
        return Registry::getConfig()->getShopUrl();
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->context->getCurrentShopId();
    }

    /**
     * @param string|null $dbGroup
     * @return string
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
}