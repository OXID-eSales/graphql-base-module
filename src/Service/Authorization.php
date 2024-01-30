<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Event\BeforeAuthorization;
use OxidEsales\GraphQL\Base\Framework\PermissionProviderInterface;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Security\AuthorizationServiceInterface;

use function array_search;
use function is_bool;

class Authorization implements AuthorizationServiceInterface
{
    /** @var array<string, array<string>> */
    private $permissions = [];

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var AccessToken */
    private $tokenService;

    /** @var LegacyService */
    private $legacyService;

    /**
     * @param PermissionProviderInterface[] $permissionProviders
     */
    public function __construct(
        iterable $permissionProviders,
        EventDispatcherInterface $eventDispatcher,
        AccessToken $tokenService,
        LegacyService $legacyService
    ) {
        foreach ($permissionProviders as $permissionProvider) {
            $this->permissions = array_merge_recursive(
                $this->permissions,
                $permissionProvider->getPermissions()
            );
        }
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenService = $tokenService;
        $this->legacyService = $legacyService;
    }

    /**
     * @param mixed $subject
     */
    public function isAllowed(string $right, $subject = null): bool
    {
        // TODO: Make usage of $subject argument
        $event = new BeforeAuthorization(
            $this->tokenService->getToken(),
            $right
        );

        $this->eventDispatcher->dispatch(
            $event
        );

        $authByEvent = $event->getAuthorized();

        if (is_bool($authByEvent)) {
            return $authByEvent;
        }

        $userId = $this->tokenService->getTokenClaim(AccessToken::CLAIM_USERID);
        $groups = $this->legacyService->getUserGroupIds($userId);

        $isAllowed = false;

        foreach ($groups as $id) {
            if (isset($this->permissions[$id]) && array_search($right, $this->permissions[$id], true) !== false) {
                $isAllowed = true;
            }
        }

        return $isAllowed;
    }
}
