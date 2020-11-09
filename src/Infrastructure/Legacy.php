<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use Exception;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\MailValidator as EhopMailValidator;
use OxidEsales\Eshop\Core\Model\ListModel as EshopListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Framework\UserData;

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
    public function login(string $username, string $password): UserData
    {
        try {
            /** @var User */
            $user = oxNew(User::class);
            $user->login($username, $password, false);
        } catch (Exception $e) {
            throw new InvalidLogin('Username/password combination is invalid');
        }

        return new UserData(
            $user->getId(),
            $this->getUserGroupIds($user)
        );
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
        /** @var UtilsObject */
        $utils = Registry::getUtilsObject();

        return $utils->generateUId();
    }

    public function isValidEmail(string $email): bool
    {
        /** @var EhopMailValidator */
        $validator = oxNew(EhopMailValidator::class);

        return $validator->isValidEmail($email);
    }

    /**
     * @return Email|object
     */
    public function getEmail()
    {
        return oxNew(Email::class);
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

    /**
     * @return array<string,string>
     */
    private function getUserGroupIds(User $user): array
    {
        /** @var EshopListModel $userGroupList */
        $userGroupList = $user->getUserGroups();

        $return = [];

        foreach ($userGroupList->getArray() as $group) {
            $return[(string) $group->getId()] = (string) $group->getId();
        }

        return $return;
    }
}
