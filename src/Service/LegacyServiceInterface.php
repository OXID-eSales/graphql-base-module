<?php

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLoginException;

interface LegacyServiceInterface
{
    public const GROUP_ADMIN = 'admin';
    public const GROUP_CUSTOMERS = 'customer';

    /**
     * @throws InvalidLoginException
     */
    public function checkCredentials(string $username, string $password): void;

    /**
     * @throws InvalidLoginException
     */
    public function getUserGroup(string $username): string;

    /**
     * @return mixed
     */
    public function getConfigParam(string $param);

    public function getShopUrl(): string;

    public function getShopId(): int;

    public function getLanguageId(): int;

    public function createUniqueIdentifier(): string;
}
