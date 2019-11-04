<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Service;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Event\BeforeAuthorizationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthorizationService implements AuthorizationServiceInterface
{
    /** @var ?Token */
    private $token = null;

    /** @var array<string, array<string>> */
    private $permissions = [];

    /** @var EventDispatcherInterface */
    private $eventDispatcher = null;

    public function __construct(
        iterable $permissionProviders,
        EventDispatcherInterface $eventDispatcher
    ) {
        /** @var \OxidEsales\GraphQL\Framework\PermissionProviderInterface $permissionProvider */
        foreach ($permissionProviders as $permissionProvider) {
            $this->permissions = array_merge_recursive(
                $this->permissions,
                $permissionProvider->getPermissions()
            );
        }
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * TODO: validate token!!
     */
    public function setToken(?Token $token = null)
    {
        $this->token = $token;
    }

    public function isAllowed(string $right): bool
    {
        if ($this->token === null) {
            return false;
        }

        $event = new BeforeAuthorizationEvent(
            $this->token,
            $right
        );

        $this->eventDispatcher->dispatch(
            BeforeAuthorizationEvent::NAME,
            $event
        );

        $authByEvent = $event->getAuthorized();
        if ($authByEvent === true) {
            return true;
        } elseif ($authByEvent === false) {
            return false;
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
