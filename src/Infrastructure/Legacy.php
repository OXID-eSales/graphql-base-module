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
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Model\ListModel as EshopListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceInterface as EhopEmailValidator;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Framework\AnonymousUserData;
use OxidEsales\GraphQL\Base\Framework\UserData;
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

    /** @var EhopEmailValidator */
    private $emailValidatorService;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        ContextInterface $context,
        EhopEmailValidator $emailValidatorService
    ) {
        $this->queryBuilderFactory   = $queryBuilderFactory;
        $this->context               = $context;
        $this->emailValidatorService = $emailValidatorService;
    }

    /**
     * @throws InvalidLogin
     */
    public function login(?string $username = null, ?string $password = null): UserDataInterface
    {
        if ($username === null || $password === null) {
            return new AnonymousUserData();
        }

        try {
            /** @var User */
            $user = oxNew(User::class);
            $user->login($username, $password, false);
        } catch (SystemComponentException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new InvalidLogin('Username/password combination is invalid');
        }

        return new UserData(
            $user->getId()
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

        /** @var User $user */
        $user = oxNew(User::class);

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
