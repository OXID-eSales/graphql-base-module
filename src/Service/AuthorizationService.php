<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Event\BeforeAuthorization;
use OxidEsales\GraphQL\Base\Framework\PermissionProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Security\AuthorizationServiceInterface;

use function array_search;
use function is_bool;

class AuthorizationService implements AuthorizationServiceInterface
{
    /** @var array<string, array<string>> */
    private $permissions = [];

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ?Token */
    private $token;

    /**
     * @param PermissionProviderInterface[] $permissionProviders
     */
    public function __construct(
        iterable $permissionProviders,
        EventDispatcherInterface $eventDispatcher
    ) {
        foreach ($permissionProviders as $permissionProvider) {
            $this->permissions = array_merge_recursive(
                $this->permissions,
                $permissionProvider->getPermissions()
            );
        }
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setToken(?Token $token = null): void
    {
        $this->token = $token;
    }

    public function isAllowed(string $right): bool
    {
        if ($this->token === null) {
            return false;
        }

        $event = new BeforeAuthorization(
            $this->token,
            $right
        );

        $this->eventDispatcher->dispatch(
            BeforeAuthorization::NAME,
            $event
        );

        $authByEvent = $event->getAuthorized();

        if (is_bool($authByEvent)) {
            return $authByEvent;
        }

        $group = $this->token->getClaim(AuthenticationService::CLAIM_GROUP);

        if (!isset($this->permissions[$group])) {
            return false;
        }

        if (array_search($right, $this->permissions[$group], true) === false) {
            return false;
        }

        return true;
    }
}
