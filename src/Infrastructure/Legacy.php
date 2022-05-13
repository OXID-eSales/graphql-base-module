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
use OxidEsales\Eshop\Core\Model\ListModel as EshopListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceInterface as EhopEmailValidator;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;

/**
 * @codeCoverageIgnore - Remove when integration tests are added to the coverage report
 */
class Legacy
{
    /** @var ContextInterface */
    private $context;

    /** @var EhopEmailValidator */
    private $emailValidatorService;

    public function __construct(
        ContextInterface $context,
        EhopEmailValidator $emailValidatorService
    ) {
        $this->context               = $context;
        $this->emailValidatorService = $emailValidatorService;
    }

    /**
     * @throws InvalidLogin
     */
    public function login(?string $username = null, ?string $password = null): User
    {
        /** @var UserModel $user */
        $user = $this->getUserModel();
        $isAnonymous = true;

        if ($username && $password) {
            try {
                $isAnonymous = false;
                $user->login($username, $password, false);
            } catch (Exception $e) {
                throw new InvalidLogin('Username/password combination is invalid');
            }
        } else {
            $user->setId(self::createUniqueIdentifier());
        }

        return new User($user, $isAnonymous);
    }

    public function getUserModel(?string $userId = null): UserModel
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
            return (int)Registry::getLang()->getBaseLanguage();
        }

        return (int)$requestParameter;
    }

    public function isValidEmail(string $email): bool
    {
        return $this->emailValidatorService->isEmailValid($email);
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
        $user = $this->getUserModel($userId);

        if (!$user->isLoaded()) {
            return ['oxidanonymous'];
        }

        /** @var EshopListModel $userGroupList */
        $userGroupList = $user->getUserGroups();

        $userGroupIds = [];

        foreach ($userGroupList->getArray() as $group) {
            $userGroupIds[] = (string)$group->getId();
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
