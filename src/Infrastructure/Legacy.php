<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use Exception;
use OxidEsales\Eshop\Application\Model\User as UserModel;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\MailValidator as EhopMailValidator;
use OxidEsales\Eshop\Core\Model\ListModel as EshopListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Framework\UserDataInterface;

/**
 * @codeCoverageIgnore - Remove when integration tests are added to the coverage report
 */
class Legacy
{
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
    public function login(?string $username = null, ?string $password = null): UserDataInterface
    {
        /** @var UserModel */
        $user        = oxNew(UserModel::class);
        $isAnonymous = true;

        if ($username && $password) {
            try {
                $isAnonymous = false;
                $user->login($username, $password, false);
            } catch (Exception $e) {
                throw new InvalidLogin('Username/password combination is invalid');
            }
        }

        return new UserDataType($user, $isAnonymous);
    }

    public function getUserModel(?string $userId): UserModel
    {
        $userModel = oxNew(UserModel::class);

        if ($userId) {
            $userModel->load($userId);
        }

        return $userModel;
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
     * @return string[]
     */
    public function getUserGroupIds(?string $userId): array
    {
        if (!$userId) {
            return [];
        }

        /** @var UserModel $user */
        $user = oxNew(UserModel::class);

        if (!$user->load($userId)) {
            return ['oxidanonymous'];
        }

        /** @var EshopListModel $userGroupList */
        $userGroupList = $user->getUserGroups();

        $userGroupIds = [];

        foreach ($userGroupList->getArray() as $group) {
            $userGroupIds[] = (string) $group->getId();
        }

        return $userGroupIds;
    }

    public static function createUniqueIdentifier(): string
    {
        /** @var UtilsObject */
        $utils = Registry::getUtilsObject();

        return $utils->generateUId();
    }
}
